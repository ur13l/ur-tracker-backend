<?php

namespace App\Http\Controllers;



use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use Mail;

class UserController extends Controller
{

  /**
   * Authenticate the user by an email and password.
   * params: [email*, password*]
   * @param  Request $request HTTP Request
   * @return *
   */
  public function login(Request $request) {
    $email = $request->email;
    $password = $request->password;
    $success = false;
    $data = null;
    $errors = [];
    if(Auth::once(["email" => $email, "password" => $password])) {
      $data = Auth::user();
      $success = true;
    } else {
      $errors[] = "Correo o contraseña incorrectos";
    }

    return response()->json(array(
      'success' => $success,
      'errors' =>$errors,
      'data' => $data
    ));

  }


  /**
   * Verify the api_token to identify if the user is valid.
   * params: [api_token*]
   * @param  Request $request Request HTTP
   * @return *
   */
  public function check(Request $request) {
    $user = Auth::guard('api')->user();
    $data = false;
    $status = 404;
    $success = false;
    $errors = [];

    if(isset($user)){
      $data = true;
      $success = true;
      $status = 200;
    }
    else {
      $errors[] = "No se encontró al usuario";
    }
    return response()->json(array(
      'success' => $success,
      'status' => $status,
      'errors' => $errors,
      'data' => $data
    ));
  }


/**
 * Api method to register users
 * params: [email*, password*, password_confirmation, name*]
 * @param  Request $request HTTP Request
 * @return *
 */
  public function register(Request $request) {
    $rules = array(
      'email' => 'unique:users|email|required',
      'password' => 'required|confirmed',
      'name' => 'required'
    );
    $validator = Validator::make($request->all(), $rules);
    $errors = [];
    $success = true;
    $data = null;
    $user = null;
    if ($validator->fails()) {
      $success = false;
      $messages = $validator->messages()->all();
      $errors = $messages;
    }
    else {
      $request->request->add(['confirmation_token' => str_random(50)]);
      $request->request->add(['active' => false]);
      $user = User::create($request->all());

      Mail::send('emails.users.confirm_user', ['user' => $user], function($m) use ($user) {
        $m->from('hello@ur-tracker.io', 'Bienvenido a URTracker');
        $m->to($user->email, $user->name);
      });
    }

    return response()->json(array(
      'success' => $success,
      'errors' => $errors,
      'data' => $user
    ));
  }


  public function confirmUser(Request $request) {
    $rules = array(
      'confirm_token' => 'required',
    );
    $validator = Validator::make($request->all(), $rules);
    $errors = [];
    $success = true;
    $data = null;
    if ($validator->fails()) {
      $success = false;
      $messages = $validator->messages()->all();
      $errors = $messages;
    }
    else {
      $user = Auth::guard('api')->user();
      if($user) {
        if($user->confirm_token == $request->confirm_token) {
          $user->active = true;
          $user->save();
          return redirect()->to(env('RESET_PASSWORD_URL') . "?confirm_token");
        }
        else {
          $success = false;
          $errors[] = "Mismatched tokens";
        }
      }
      else {
        $success = false;
        $errors[] = "User not found";
      }
    }

    return response()->json(array(
      'success' => $success,
      'errors' => $errors,
      'data' => $data
    ));
  }
}

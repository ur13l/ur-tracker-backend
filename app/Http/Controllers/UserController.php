<?php

namespace App\Http\Controllers;



use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{

  /**
   * Authenticate the user by an email and password.
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
    if ($validator->fails()) {
      $success = false;
      $messages = $validator->messages()->all();
      $errors = $messages;
    }
    else {
      $request->request->add(['confirmation_token' => str_random(50)]);
      $request->request->add(['active' => false]);
      $data = User::create($request->all());
    }

    return response()->json(array(
      'success' => $success,
      'errors' => $errors,
      'data' => $data
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
          return redirect()->to('https://google.com');
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

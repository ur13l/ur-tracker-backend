<?php

namespace App\Http\Controllers;



use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
}

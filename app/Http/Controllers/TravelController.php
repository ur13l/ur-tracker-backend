<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use Illuminate\Support\Facades\Auth;

class TravelController extends Controller
{
    public function get(Request $request) {
      $user = Auth::guard('api')->user();

      return response()->json(array(
        'data' => $user->travels()->paginate(10),
        'success' => true,
        'status' => 200,
        'errors' => []
      ));
    }
}

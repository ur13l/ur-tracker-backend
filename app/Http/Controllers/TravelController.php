<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use Illuminate\Support\Facades\Auth;

class TravelController extends Controller
{
    /**
     * Retrieve the list of travels by user's api_token.
     * @param  Request $request HTTP Request
     * @return *           
     */
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

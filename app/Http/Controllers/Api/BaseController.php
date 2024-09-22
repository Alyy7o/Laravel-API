<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($result, $messge){
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $messge,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessge = [], $code = 404){
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessge)){
            $response['data'] = $errorMessge;
        }

        return response()->json($response, 200);
    }


}

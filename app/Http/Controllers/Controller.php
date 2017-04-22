<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
  public function success($message, $code){
      return response()->json(['message' => $message], $code);
  }

  public function successData($data, $code){
      return response()->json($data, $code);
  }

  public function error($error, $code){
      return response()->json(['error' => $error], $code);
  }
}

<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
  public function success($message = 'Success', $code = '200'){
      return response()->json(['message' => $message], $code);
  }

  public function successData($data = [], $code = '200'){
      return response()->json($data, $code);
  }

  public function error($error = 'Error', $code = '404'){
      return response()->json(['error' => $error], $code);
  }

  public function computeServiceFee($total_price = 0, $reward = 0)
  {
    $service_fee = ($total_price + $reward) * 0.1;
    return $service_fee;
  }

  public function computeTotal($total_price = 0, $reward = 0, $service_fee = 0)
  {
    $total = $total_price + $reward + $service_fee;
    return $total;
  }
}

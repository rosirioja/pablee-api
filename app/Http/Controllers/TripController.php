<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;

class TripController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getRecent()
    {
      $data = Trip::orderBy('id', 'desc')->take(6)->get();
      return $this->successData($data, 200);
    }
}

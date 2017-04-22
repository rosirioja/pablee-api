<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\Status;

class RequestController extends Controller
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

    public function index()
    {
      $data = RequestModel::all();

      return $this->successData($data, 200);
    }

    public function index()
    {
      $data = RequestModel::orderBy('id', 'desc')->take(6)->get();

      return $this->successData($data, 200);
    }

    public function store(Request $request)
    {
      $this->validateRequest($request);

      $price = $request->input('price');
      $reward = $request->input('reward');
      $service_fee = ($price + $reward) * 0.1;
      $total = $price + $reward + $service_fee;

      $requestModel =  new RequestModel;
      $requestModel->fill($request->all());
      $requestModel->service_fee = $service_fee;
      $requestModel->total_amount = $total;
      $requestModel->status_id = Status::ofName('open')->id;
      $requestModel->save();

      return $this->success('Success', 201);
    }

    public function validateRequest($request)
    {
      $rules = [
        'uuid' => 'required',
        'title' => 'required',
        'description' => 'required',
        'link' => 'required',
        'image_url' => 'required',
        'quantity' => 'required|integer',
        'price' => 'required|numeric',
        'reward' => 'required|numeric',
        'location' => 'required',
        // 'needed_at' optional
      ];

      $this->validate($request, $rules);

    }
}

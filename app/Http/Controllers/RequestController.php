<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\Status;
use App\Models\Offer;
use DB;

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

    public function getRecent()
    {
      $open_id = Status::ofName('open')->id;
      $offered_id = Status::ofName('offered')->id;

      $data = DB::select("SELECT * FROM requests r WHERE r.status_id = {$open_id} OR r.status_id = {$offered_id} order by r.id desc limit 6");

      return $this->successData($data, 200);
    }

    public function view($id)
    {
      $request = RequestModel::find($id);

      if (empty($request)) {
        return $this->error('Invalid Request', 404);
      }

      $request = DB::select("SELECT r.*, s.display_name as status FROM requests r LEFT JOIN status s ON r.status_id = s.id WHERE r.id = {$id}");

      // $offers = Offer::where('request_id', $id)->get();
      $offers = DB::select("SELECT o.*, s.display_name as status, t.travel_from, t.travel_to FROM offers o LEFT JOIN status s ON o.status_id = s.id LEFT JOIN trips t ON t.id = o.trip_id");

      $data = [
        'request' => $request,
        'offers' => $offers
      ];

      return $this->successData($data, 200);
    }

    public function store(Request $request)
    {
      $this->_validateRequest($request);

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

    public function update(Request $request, $id)
    {
      $requestModel = RequestModel::find($id);
      if (empty($requestModel)) {
        return $this->error('Invalid Request', 404);
      }

      $this->_validateRequest($request);

      $price = $request->input('price');
      $reward = $request->input('reward');
      $service_fee = ($price + $reward) * 0.1;
      $total = $price + $reward + $service_fee;

      $requestModel->fill($request->all());
      $requestModel->service_fee = $service_fee;
      $requestModel->total_amount = $total;
      $requestModel->update();

      return $this->success('Success', 201);
    }

    public function cancel($id)
    {
      $requestModel = RequestModel::find($id);
      if (empty($requestModel)) {
        return $this->error('Invalid Request', 404);
      }

      $requestModel->status_id = Status::ofName('cancelled')->id;
      $requestModel->update();

      return $this->success('Success', 201);
    }

    public function updateStatus(Request $request, $id)
    {
      $requestModel = RequestModel::find($id);
      if (empty($requestModel)) {
        return $this->error('Invalid Request', 404);
      }

      $requestModel->status_id = Status::ofName($request->input('status_name'))->id;
      $requestModel->update();

      return $this->success('Success', 201);
    }

    public function _validateRequest($request)
    {
      $rules = [
        'uuid' => 'required',
        'title' => 'required',
        'description' => 'required',
        'link' => 'required',
        'image_url' => 'required',
        'quantity' => 'required|integer',
        'currency' => 'required',
        'price' => 'required|numeric',
        'reward' => 'required|numeric',
        'deliver_from' => 'required',
        'deliver_to' => 'required',
        // 'needed_at' optional
      ];

      $this->validate($request, $rules);

    }
}

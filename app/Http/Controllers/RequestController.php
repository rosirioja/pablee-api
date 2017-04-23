<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use App\Models\Status;
use App\Models\Offer;
use App\Models\Payment;
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

    public function getSearch($keyword)
    {
      $keyword = urldecode($keyword);
      $data = RequestModel::where('title', 'like', "%{$keyword}%")->orWhere('deliver_from', 'like', "%{$keyword}%")->orWhere('deliver_to', 'like', "%{$keyword}%")->get();

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

      $requestModel =  new RequestModel;
      $requestModel->fill($request->all());
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

      $requestModel->fill($request->all());
      $requestModel->update();

      return $this->success('Success', 201);
    }

    public function postOffer(Request $request, $id)
    {
      $this->_validateOffer($request);

      $requestModel = RequestModel::find($id);
      if (empty($requestModel)) {
        return $this->error('Invalid Request', 404);
      }

      // Unauth!
      if ($request->input('uuid') != $requestModel->uuid) {
        return $this->error('Unauthorized', 401);
      }

      $offer_id = $request->input('offer_id');
      $offer = Offer::find($offer_id);
      if (empty($offer)) {
        return $this->error('Invalid Offer', 404);
      }

      // Update Request Status
      $requestModel->status_id = Status::ofName('on_purchase')->id;
      $requestModel->update();

      // Set all offers as rejected
      Offer::where('request_id', $id)->update([
        'status_id' => Status::ofName('rejected')->id
      ]);

      // Set chosen offer as accepted
      $offer->status_id = Status::ofName('accepted')->id;
      $offer->update();

      $quantity = $requestModel->quantity;
      $price = $requestModel->price;
      $total_price = $quantity * $price;
      $reward = $offer->reward;
      $service_fee = $this->computeServiceFee($total_price, $reward);
      $total = $this->computeTotal([$total_price, $reward, $service_fee]);

      // Insert into Payments Table
      $payment = new Payment;
      $payment->request_id = $id;
      $payment->offer_id = $offer_id;
      $payment->type = 'cash-in';
      $payment->currency = $request->input('currency');
      $payment->quantity = $quantity;
      $payment->price = $price;
      $payment->total_price = $total_price;
      $payment->reward = $reward;
      $payment->service_fee = $service_fee;
      $payment->total_amount = $total;
      $payment->save();

      return $this->success('Success', 201);
    }

    public function postComplete(Request $request, $id)
    {
      $this->_validateComplete($request);

      $requestModel = RequestModel::find($id);
      if (empty($requestModel)) {
        return $this->error('Invalid Request', 404);
      }

      // Unauth!
      if ($request->input('uuid') != $requestModel->uuid) {
        return $this->error('Unauthorized', 401);
      }

      // Update Request Status
      $requestModel->status_id = Status::ofName('completed')->id;
      $requestModel->update();

      $shopper_payment = Payment::where('request_id', $id)->get()->first();

      // Pay the Traveller
      $payment = new Payment;
      $payment->request_id = $id;
      $payment->offer_id = $shopper_payment->offer_id;
      $payment->type = 'payout';
      $payment->currency = $request->input('currency');
      $payment->quantity = $shopper_payment->quantity;
      $payment->price = $shopper_payment->price;
      $payment->total_price = $shopper_payment->total_price;
      $payment->reward = $shopper_payment->reward;
      $payment->service_fee = 0;
      $payment->total_amount = $this->computeTotal([$shopper_payment->total_price, $shopper_payment->reward]);
      $payment->save();

      return $this->success('Success', 201);
    }

    public function updateStatus(Request $request, $id)
    {
      $requestModel = RequestModel::find($id);
      if (empty($requestModel)) {
        return $this->error('Invalid Request', 404);
      }

      $status = $request->input('status_name');

      $requestModel->status_id = Status::ofName($status)->id;
      $requestModel->update();

      return $this->success('Success', 201);
    }

    public function _validateRequest($request = [])
    {
      $rules = [
        'uuid' => 'required',
        'title' => 'required',
        'description' => 'required',
        // 'link' optional
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

    public function _validateComplete($request = [])
    {
      $rules = [
        'uuid' => 'required',
        'currency' => 'required'
      ];

      $this->validate($request, $rules);
    }
}

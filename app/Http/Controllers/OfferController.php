<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Status;
use App\Models\Trip;
use App\Models\Request as RequestModel;
use DB;

class OfferController extends Controller
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

    public function store(Request $request)
    {
      $this->_validateRequest($request);

      $uuid = $request->input('uuid');
      $request_id = $request->input('request_id');
      $travel_from = $request->input('travel_from');
      $travel_to = $request->input('travel_to');
      $open_id = Status::ofName('open')->id;
      $offered_id = Status::ofName('offered')->id;

      $requestModel = DB::select("SELECT * FROM requests r WHERE id = {$request_id} AND (r.status_id = {$open_id} OR r.status_id = {$offered_id})");
      if (empty($requestModel)) {
        return $this->error('Invalid Request', 404);
      }

      // Check if Trip exists
      $trip = Trip::where('uuid', $uuid)->where('travel_from', $travel_from)->where('travel_to', $travel_to)->count();

      if (! $trip) {
        $trip = new Trip;
        $trip->uuid = $uuid;
        $trip->travel_from = $travel_from;
        $trip->travel_to = $travel_to;
        $trip->travel_date = $request->input('travel_date');
        $trip->save();
      } else {
        $trip = Trip::where('uuid', $uuid)->where('travel_from', $travel_from)->where('travel_to', $travel_to)->get()->first();
      }
      
      $offer = new Offer;
      $offer->uuid = $uuid;
      $offer->request_id = $request_id;
      $offer->trip_id = $trip->id;
      $offer->currency = $request->input('currency');
      $offer->reward = $request->input('reward');
      $offer->delivery_date = $request->input('delivery_date');
      $offer->status_id = Status::ofName('active')->id;
      $offer->save();

      $requestModel = RequestModel::find($request_id);
      $requestModel->status_id = Status::OfName('offered')->id;
      $requestModel->update();

      return $this->success('Success', 201);
    }

    public function update(Request $request, $id)
    {
      $offer = Offer::find($id);
      if (empty($offer)) {
        return $this->error('Invalid Offer', 404);
      }

      $offer->fill($request->all());
      $offer->update();

      return $this->success('Success', 201);
    }

    public function cancel($id)
    {
      $offer = Offer::find($id);
      if (empty($offer)) {
        return $this->error('Invalid Offer', 404);
      }

      $offer->status_id = Status::ofName('cancelled')->id;
      $offer->update();

      return $this->success('Success', 201);
    }

    public function _validateRequest($request)
    {
      $rules = [
        'uuid' => 'required',
        'request_id' => 'required|integer',
        'currency' => 'required',
        'reward' => 'required|numeric',
        'travel_from' => 'required',
        'travel_to' => 'required',
        'travel_date' => 'required|date'
      ];

      $this->validate($request, $rules);

    }
}

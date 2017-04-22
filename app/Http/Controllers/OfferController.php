<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Status;

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

      $offer = new Offer;
      $offer->fill($request->all());
      $offer->status_id = Status::ofName('active')->id;
      $offer->save();

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
        'trip_id' => 'required|integer',
        'reward' => 'required|numeric'
      ];

      $this->validate($request, $rules);

    }
}

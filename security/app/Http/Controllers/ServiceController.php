<?php

namespace App\Http\Controllers;
use App\Models\Insurance;
use App\Models\InsuranceServ;
use App\Models\Order2;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
   public function show()
   {

    return view('insurances',['insurances'=>insurance::all()]);
   }

   public function showhealth()
   {

    return view('health-insurance',['insurances'=>InsuranceServ::all()]);
   }
   public function showcar()
   {

    return view('Car-insurance-based-on-mileage',['insurances'=>InsuranceServ::all()]);
   }


   public function cancel()
   {

    return view('Cancel-Insurance',['orders'=>Order2::all()]);
   }
   public function canceldelete()
   {
     $o = Order2::where('id',request('rowid'));
      $o->delete();
      return redirect('/')->with('success', 'User registered successfully!');
    
   }

}

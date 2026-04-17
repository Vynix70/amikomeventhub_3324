<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function show(){
        return view('event-detail');
    }

    public function chekout(){
        return view('checkout');
    }
}

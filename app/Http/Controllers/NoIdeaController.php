<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NoIdeaController extends Controller
{
    public function index(){
        return view('no-idea');
    }

    public function stepTwo(){
        return view('step-two');
    }
}

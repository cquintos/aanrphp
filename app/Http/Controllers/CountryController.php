<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = \App\Country::all();
        return response()->json([
            'body' => view('pages.country', compact('countries'))->render(),
            'countries' => $countries,
        ]);
    }
}

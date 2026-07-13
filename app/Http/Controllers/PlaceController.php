<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        return PlaceResource::collection(Place::paginate(15));
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Place $place)
    {
        //
    }

    public function update(Request $request, Place $place)
    {
        //
    }

    public function destroy(Place $place)
    {
        //
    }
}

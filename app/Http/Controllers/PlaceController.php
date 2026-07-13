<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlaceRequest;
use App\Http\Requests\UpdatePlaceRequest;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        $places = Place::query()
            ->when($request->filled('name'), fn ($query) => $query->where('name', 'ilike', "%{$request->string('name')}%"))
            ->paginate(15);

        return PlaceResource::collection($places);
    }

    public function store(StorePlaceRequest $request)
    {
        $place = Place::create($request->validated());

        return (new PlaceResource($place))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Place $place)
    {
        return new PlaceResource($place);
    }

    public function update(UpdatePlaceRequest $request, Place $place)
    {
        $place->update($request->validated());

        return new PlaceResource($place);
    }

    public function destroy(Place $place)
    {
        //
    }
}

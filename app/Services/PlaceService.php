<?php

namespace App\Services;

use App\Models\Place;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PlaceService
{
    public function list(?string $name): LengthAwarePaginator
    {
        return Place::query()
            ->when($name, fn ($query, $name) => $query->where('name', 'ilike', "%{$name}%"))
            ->paginate(15);
    }

    public function create(array $data): Place
    {
        return Place::create($data);
    }

    public function update(Place $place, array $data): Place
    {
        $place->update($data);

        return $place;
    }

    public function delete(Place $place): void
    {
        $place->delete();
    }
}

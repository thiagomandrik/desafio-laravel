<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowPlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_show_a_place(): void
    {
        $place = Place::factory()->create();

        $response = $this->getJson("/api/places/{$place->id}");

        $response->assertOk()->assertJson([
            'data' => [
                'id' => $place->id,
                'name' => $place->name,
                'slug' => $place->slug,
                'city' => $place->city,
                'state' => $place->state,
            ],
        ]);
    }

    public function test_it_returns_404_for_a_nonexistent_place(): void
    {
        $response = $this->getJson('/api/places/999');

        $response->assertNotFound();
    }
}

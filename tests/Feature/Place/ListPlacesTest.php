<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPlacesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_an_empty_list_when_there_are_no_places(): void
    {
        $response = $this->getJson('/api/places');

        $response->assertOk()->assertJson([
            'data' => [],
        ]);
    }

    public function test_it_filters_places_by_partial_case_insensitive_name(): void
    {
        Place::factory()->create(['name' => 'Praia Mole', 'slug' => 'praia-mole']);
        Place::factory()->create(['name' => 'Praia da Joaquina', 'slug' => 'praia-da-joaquina']);
        Place::factory()->create(['name' => 'Lagoinha do Leste', 'slug' => 'lagoinha-do-leste']);

        $response = $this->getJson('/api/places?name=PRAIA');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['name' => 'Praia Mole']);
        $response->assertJsonFragment(['name' => 'Praia da Joaquina']);
        $response->assertJsonMissing(['name' => 'Lagoinha do Leste']);
    }
}

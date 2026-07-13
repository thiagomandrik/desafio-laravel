<?php

namespace Tests\Feature\Place;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_place(): void
    {
        $payload = [
            'name' => 'Lagoinha do leste',
            'slug' => 'lagoinha-do-leste',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ];

        $response = $this->postJson('/api/places', $payload);

        $response->assertCreated()->assertJson([
            'data' => $payload,
        ]);

        $this->assertDatabaseHas('places', $payload);
    }

    public function test_it_requires_name_to_create_a_place(): void
    {
        $response = $this->postJson('/api/places', [
            'slug' => 'praia-mole',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_it_rejects_an_invalid_state(): void
    {
        $response = $this->postJson('/api/places', [
            'name' => 'Praia Mole',
            'slug' => 'praia-mole',
            'city' => 'Florianópolis',
            'state' => 'XX',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('state');
    }

    public function test_it_rejects_a_duplicate_slug(): void
    {
        $existing = [
            'name' => 'Praia Mole',
            'slug' => 'praia-mole',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ];
        $this->postJson('/api/places', $existing)->assertCreated();

        $response = $this->postJson('/api/places', [
            'name' => 'Praia Mole 2',
            'slug' => 'praia-mole',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('slug');
    }

    public function test_it_rejects_a_slug_that_is_not_kebab_case(): void
    {
        $response = $this->postJson('/api/places', [
            'name' => 'Praia Mole',
            'slug' => 'Praia Mole',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('slug');
    }
}

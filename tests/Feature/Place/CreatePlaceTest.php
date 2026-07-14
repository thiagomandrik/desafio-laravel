<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_place(): void
    {
        $payload = [
            'name' => 'Lagoinha do leste',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ];

        $response = $this->postJson('/api/places', $payload);

        $response->assertCreated()->assertJson([
            'data' => $payload + ['slug' => 'lagoinha-do-leste'],
        ]);

        $this->assertDatabaseHas('places', $payload + ['slug' => 'lagoinha-do-leste']);
    }

    public function test_it_requires_name_to_create_a_place(): void
    {
        $response = $this->postJson('/api/places', [
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_it_rejects_an_invalid_state(): void
    {
        $response = $this->postJson('/api/places', [
            'name' => 'Praia Mole',
            'city' => 'Florianópolis',
            'state' => 'XX',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('state');
    }

    public function test_it_generates_a_slug_from_the_name(): void
    {
        $response = $this->postJson('/api/places', [
            'name' => 'Praia Mole',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertCreated()->assertJson([
            'data' => ['slug' => 'praia-mole'],
        ]);
    }

    public function test_it_generates_a_unique_slug_when_the_name_already_exists(): void
    {
        Place::factory()->create(['name' => 'Praia Mole', 'slug' => 'praia-mole']);

        $response = $this->postJson('/api/places', [
            'name' => 'Praia Mole',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertCreated()->assertJson([
            'data' => ['slug' => 'praia-mole-2'],
        ]);
    }
}

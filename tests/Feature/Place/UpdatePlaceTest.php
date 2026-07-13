<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_update_a_place(): void
    {
        $place = Place::factory()->create([
            'name' => 'Praia Mole',
            'slug' => 'praia-mole',
        ]);

        $response = $this->putJson("/api/places/{$place->id}", [
            'name' => 'Praia Mole Atualizada',
            'slug' => 'praia-mole-atualizada',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertOk()->assertJson([
            'data' => [
                'id' => $place->id,
                'name' => 'Praia Mole Atualizada',
                'slug' => 'praia-mole-atualizada',
            ],
        ]);

        $this->assertDatabaseHas('places', [
            'id' => $place->id,
            'name' => 'Praia Mole Atualizada',
            'slug' => 'praia-mole-atualizada',
        ]);
    }

    public function test_it_returns_404_when_updating_a_nonexistent_place(): void
    {
        $response = $this->putJson('/api/places/999', [
            'name' => 'Praia Mole',
            'slug' => 'praia-mole',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertNotFound();
    }

    public function test_it_allows_keeping_the_same_slug_when_updating(): void
    {
        $place = Place::factory()->create([
            'name' => 'Praia Mole',
            'slug' => 'praia-mole',
            'city' => 'Florianópolis',
        ]);

        $response = $this->putJson("/api/places/{$place->id}", [
            'name' => 'Praia Mole',
            'slug' => 'praia-mole',
            'city' => 'Palhoça',
            'state' => 'SC',
        ]);

        $response->assertOk()->assertJson([
            'data' => ['slug' => 'praia-mole', 'city' => 'Palhoça'],
        ]);

        $this->assertDatabaseHas('places', ['id' => $place->id, 'city' => 'Palhoça']);
    }

    public function test_it_rejects_updating_to_a_slug_used_by_another_place(): void
    {
        Place::factory()->create(['name' => 'Praia Mole', 'slug' => 'praia-mole']);
        $place = Place::factory()->create(['name' => 'Praia da Joaquina', 'slug' => 'praia-da-joaquina']);

        $response = $this->putJson("/api/places/{$place->id}", [
            'name' => 'Praia da Joaquina',
            'slug' => 'praia-mole',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('slug');
    }
}

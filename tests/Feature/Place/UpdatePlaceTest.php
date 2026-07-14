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
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertOk()->assertJson([
            'data' => [
                'id' => $place->id,
                'name' => 'Praia Mole Atualizada',
            ],
        ]);

        $this->assertDatabaseHas('places', [
            'id' => $place->id,
            'name' => 'Praia Mole Atualizada',
        ]);
    }

    public function test_it_returns_404_when_updating_a_nonexistent_place(): void
    {
        $response = $this->putJson('/api/places/999', [
            'name' => 'Praia Mole',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertNotFound()->assertJson(['message' => 'Recurso não encontrado.']);
    }

    public function test_it_does_not_change_the_slug_when_updating_the_name(): void
    {
        $place = Place::factory()->create([
            'name' => 'Praia Mole',
            'slug' => 'praia-mole',
        ]);

        $response = $this->putJson("/api/places/{$place->id}", [
            'name' => 'Praia Mole Atualizada',
            'city' => 'Florianópolis',
            'state' => 'SC',
        ]);

        $response->assertOk()->assertJson([
            'data' => ['slug' => 'praia-mole'],
        ]);

        $this->assertDatabaseHas('places', ['id' => $place->id, 'slug' => 'praia-mole']);
    }
}

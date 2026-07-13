<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletePlaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_delete_a_place(): void
    {
        $place = Place::factory()->create();

        $response = $this->deleteJson("/api/places/{$place->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('places', ['id' => $place->id]);
    }

    public function test_it_returns_404_when_deleting_a_nonexistent_place(): void
    {
        $response = $this->deleteJson('/api/places/999');

        $response->assertNotFound();
    }

    public function test_a_deleted_place_no_longer_appears_in_show_or_list(): void
    {
        $deleted = Place::factory()->create(['name' => 'Praia Mole', 'slug' => 'praia-mole']);
        $kept = Place::factory()->create(['name' => 'Praia da Joaquina', 'slug' => 'praia-da-joaquina']);

        $this->deleteJson("/api/places/{$deleted->id}")->assertNoContent();

        $this->getJson("/api/places/{$deleted->id}")->assertNotFound();

        $response = $this->getJson('/api/places');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $kept->id]);
        $response->assertJsonMissing(['id' => $deleted->id]);
    }
}

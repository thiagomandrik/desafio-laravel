<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_an_empty_list_when_there_are_no_places(): void
    {
        $response = $this->getJson('/api/places');

        $response->assertOk()->assertJson([
            'data' => [],
        ]);
    }
}

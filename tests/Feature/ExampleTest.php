<?php

namespace Tests\Feature;

use Database\Seeders\CmsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(CmsSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

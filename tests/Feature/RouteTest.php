<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class RouteTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRoot()
    {
        $this->assertTrue(true);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Laravel');
    }

    public function testHome()
    {
        $this->assertTrue(true);
        $response = $this->get('/home');
        $response->assertStatus(302);
        $response->assertSee('/login');
    }
}

<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelloWorldTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_returns_successful_response()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}
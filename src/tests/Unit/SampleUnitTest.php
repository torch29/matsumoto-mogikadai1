<?php

namespace Tests\Feature;

use Tests\TestCase;

class SampleUnitTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /* Unitフォルダを維持するためのダミーテスト */
    public function test_access_successfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}

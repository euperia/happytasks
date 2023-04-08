<?php

namespace Tests\Feature\Http\Controllers\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StatusControllerTest extends TestCase
{
    public function testStatusAuthFail()
    {
        $this->markTestIncomplete('not finished yet');
        $url = route('api.status.get', ['id' => 1]);

        $response = $this->get($url);


        $this->assertSame(403, $response->status());



    }


}

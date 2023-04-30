<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected User $user;

    protected function setUpUser()
    {
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user, ['*']);
    }
}

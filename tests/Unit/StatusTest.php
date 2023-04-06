<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Status;
use App\Models\User;
use Tests\TestCase;

class StatusTest extends TestCase
{
    use RefreshDatabase;

    protected function user()
    {
        return (User::factory()->create());
    }

    public function test_it_fails_creating_status_without_valid_user()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing authenticated user');

        $data = ['name' => 'Test', 'position' => 1];
        Status::create($data);
    }

    public function test_it_creates_status_with_uuid_and_user(): void
    {


        $user = $this->user();
        $this->actingAs($user);

        $data = ['name' => 'Test', 'position' => 1];
        $status = Status::create($data);

        $this->assertSame($status->user_id, $user->id);
        $this->assertSame($status->name, $data['name']);
        $this->assertSame($status->position, $data['position']);
    }
}

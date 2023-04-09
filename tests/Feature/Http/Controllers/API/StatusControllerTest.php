<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StatusControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStatusAuthFail()
    {
        $url = route('api.status.get', ['status' => '1ba5e529-b47f-4124-afe1-4bd6cdd4c969']);
        $response = $this->get($url);
        $this->assertSame(302, $response->status());
    }


    public function test_create_new_status_validation()
    {

        $user = User::factory()->create();
        // add three new statuses so that we can check the position update
        DB::table('statuses')->insert([
            ['id' => (string)Str::uuid(), 'name' => 'Status 1', 'position' => 1, 'user_id' => $user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 2', 'position' => 2, 'user_id' => $user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 3', 'position' => 3, 'user_id' => $user->id, 'created_at' => now()]
        ]);

        Sanctum::actingAs($user, ['*']);

        // validate empty input
        $formData = [
            'name' => '',
            'position' => ''
        ];

        $uri = route('api.status.create');
        $response = $this->postJson($uri, $formData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'errors' => [
                'name' => [
                    0 => 'Status name is required.'
                ],
                'position' => [
                    0 => 'Position is required.'
                ]
            ]
        ]);

        // validate the name exists
        $formData = [
            'name' => 'Status 2',
            'position' => 4
        ];
        $uri = route('api.status.create');
        $response = $this->postJson($uri, $formData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'errors' => [
                'name' => [
                    0 => 'Status name already used.'
                ]
            ]
        ]);
    }

    public function test_create_new_status()
    {
        $user = User::factory()->create();

        // add three new statuses so that we can check the position update

        DB::table('statuses')->insert([
            ['id' => (string)Str::uuid(), 'name' => 'Status 1', 'position' => 1, 'user_id' => $user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 2', 'position' => 2, 'user_id' => $user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 3', 'position' => 3, 'user_id' => $user->id, 'created_at' => now()]
        ]);

        Sanctum::actingAs($user, ['*']);

        $formData = [
            'name' => 'New Status',
            'position' => 2
        ];

        $uri = route('api.status.create');

        $response = $this->postJson($uri, $formData);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($formData);
        $this->assertDatabaseHas('statuses', $formData);

        // validate the positions were updated
        $this->assertDatabaseHas('statuses', ['name' => 'Status 1', 'position' => 1]);
        $this->assertDatabaseHas('statuses', ['name' => 'Status 2', 'position' => 3]);
        $this->assertDatabaseHas('statuses', ['name' => 'Status 3', 'position' => 4]);
    }


}

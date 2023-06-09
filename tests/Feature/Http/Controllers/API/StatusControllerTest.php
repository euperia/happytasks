<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function test_list_statuses()
    {
        $this->setUpUser();

        // add three new statuses so that we can check the position update
        DB::table('statuses')->insert([
            ['id' => (string)Str::uuid(), 'name' => 'Status 1', 'position' => 1, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 2', 'position' => 2, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 3', 'position' => 3, 'user_id' => $this->user->id, 'created_at' => now()]
        ]);

        $statuses = Status::all();

        $uri = route('api.status.list');
        $response = $this->getJson($uri);

        $response->assertOk();
        $response->assertSimilarJson([
            'data' => [
                [
                    'id' => $statuses[0]->id,
                    'name' => $statuses[0]->name,
                    'position' => $statuses[0]->position
                ],
                [
                    'id' => $statuses[1]->id,
                    'name' => $statuses[1]->name,
                    'position' => $statuses[1]->position
                ],
                [
                    'id' => $statuses[2]->id,
                    'name' => $statuses[2]->name,
                    'position' => $statuses[2]->position
                ],
            ],
            'links' => [
                'self' => 'link-value',
            ]
        ]);

    }

    public function test_get_a_status()
    {
        $this->setUpUser();

        // add three new statuses so that we can check the position update
        DB::table('statuses')->insert([
            ['id' => (string)Str::uuid(), 'name' => 'Status 1', 'position' => 1, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 2', 'position' => 2, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 3', 'position' => 3, 'user_id' => $this->user->id, 'created_at' => now()]
        ]);

        $status = Status::where('name', 'Status 2')->first();

        $uri = route('api.status.get', [$status]);
        $response = $this->getJson($uri);

        $response->assertOk();
        $response->assertJson([
            'id' => $status->id,
            'name' => $status->name,
            'position' => $status->position,
        ]);
    }


    public function test_create_new_status_validation()
    {

        $this->setUpUser();

        // add three new statuses so that we can check the position update
        DB::table('statuses')->insert([
            ['id' => (string)Str::uuid(), 'name' => 'Status 1', 'position' => 1, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 2', 'position' => 2, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 3', 'position' => 3, 'user_id' => $this->user->id, 'created_at' => now()]
        ]);

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
        $this->setUpUser();

        // add three new statuses so that we can check the position update

        DB::table('statuses')->insert([
            ['id' => (string)Str::uuid(), 'name' => 'Status 1', 'position' => 1, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 2', 'position' => 2, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 3', 'position' => 3, 'user_id' => $this->user->id, 'created_at' => now()]
        ]);

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

    public function test_update_existing_status()
    {
        // test we can update an existing status

        // 1. test status exists
        // 2. test validation
        // 3. test we can't update to an existing name

        $this->setUpUser();

        // add three new statuses so that we can check the position update

        DB::table('statuses')->insert([
            ['id' => (string)Str::uuid(), 'name' => 'Status 1', 'position' => 1, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 2', 'position' => 2, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 3', 'position' => 3, 'user_id' => $this->user->id, 'created_at' => now()]
        ]);

        // validate empty data
        $formData = [
            'name' => '',
            'position' => ''
        ];

        $status = Status::where('name', 'Status 2')->first();

        $uri = route('api.status.update', [$status]);
        $response = $this->putJson($uri, $formData);

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

        // validate name exists already
        $formData = [
            'name' => 'Status 1',
            'position' => '5'
        ];

        $status = Status::where('name', 'Status 2')->first();

        $uri = route('api.status.update', [$status]);
        $response = $this->putJson($uri, $formData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'errors' => [
                'name' => [
                    0 => 'Status name already used.'
                ]
            ]
        ]);

        // Update name only
        $formData = [
            'name' => 'Updated Status',
            'position' => 2
        ];

        $status = Status::where('name', 'Status 2')->first();

        $uri = route('api.status.update', [$status]);
        $response = $this->putJson($uri, $formData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($formData);

        $this->assertDatabaseHas('statuses', $formData);


        // Update position only
        $formData = [
            'name' => 'Updated Status',
            'position' => 1
        ];

        $status->refresh();

        $uri = route('api.status.update', [$status]);
        $response = $this->putJson($uri, $formData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson($formData);

        $this->assertDatabaseHas('statuses', $formData);
        $this->assertDatabaseHas('statuses', ['name' => 'Status 1', 'position' => 2]);
        $this->assertDatabaseHas('statuses', ['name' => 'Status 3', 'position' => 4]);

    }

    public function test_delete_a_status()
    {
        $this->setUpUser();
        // add three new statuses so that we can check the position update
        DB::table('statuses')->insert([
            ['id' => (string)Str::uuid(), 'name' => 'Status 1', 'position' => 1, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 2', 'position' => 2, 'user_id' => $this->user->id, 'created_at' => now()],
            ['id' => (string)Str::uuid(), 'name' => 'Status 3', 'position' => 3, 'user_id' => $this->user->id, 'created_at' => now()]
        ]);

        $status = Status::where('name', 'Status 1')->first();

        $taskId = (string)Str::uuid();

        DB::table('tasks')->insert([
            'id' => $taskId,
            'user_id' => $this->user->id,
            'name' => 'Go to the store',
            'status_id' => $status->id,
        ]);

        $uri = route('api.status.delete', [$status]);
        $response = $this->deleteJson($uri);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['message' => 'Cannot delete a status with active tasks']);

        DB::table('tasks')->delete($taskId);

        $response = $this->deleteJson($uri);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('statuses', ['id' => $status->id, 'name' => $status->name, 'user_id' => $this->user->id]);
    }
}

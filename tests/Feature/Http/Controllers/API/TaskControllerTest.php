<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Http\Resources\DueAtResource;
use App\Models\Category;
use App\Models\Status;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_task()
    {
        $this->setUpUser();

        $category = Category::factory()->create();
        $status = Status::factory()->create();
        $model = Task::factory()->create(['category_id' => $category->id, 'status_id' => $status->id]);

        $uri = route('api.task.get', $model);

        $response = $this->get($uri);

        $response->assertOk();
        $response->assertJson([
            'data' => [
                'id' => $model->id,
                'name' => $model->name,
                'description' => $model->description,
                'status' => [
                    'id' => $status->id,
                    'name' => $status->name
                ],
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name
                ]
            ]
        ]);

    }

    public function test_get_task_list()
    {
        $this->setUpUser();

        Task::factory(5)->create();

        $expectedTasks = Task::orderBy('due_at', 'asc')
            ->get()
            ->map(function ($task) {
                return [
                    'category' => [
                        'id' => $task->category->id,
                        'name' => $task->category->name,
                        'description' => $task->category->description,
                        'parent_id' => $task->category->parent_id,
                        'position' => $task->category->position,
                    ],

                    'created_at' => $task->created_at->timestamp,
                    'description' => $task->description,
//                    'due_at' => $task->due_at->getTimestamp(),
                    'duration' => $task->duration,
                    'id' => $task->id,
                    'name' => $task->name,
                    'notes' => $task->notes,
                    'status' => [
                        'id' => $task->status->id,
                        'name' => $task->status->name,
                        'position' => $task->status->position,
                    ],
                    'updated_at' => $task->updated_at->timestamp,
                    'url' => $task->url,

                ];
            })->toArray();

        $otherUser = User::factory()->create();
        $otherUserTask = Task::factory(1)->create(['user_id' => $otherUser->id])->first();

        $uri = route('api.task.list');

        $response = $this->get($uri);

        $response->assertOk();
        $response->assertJson(['data' => $expectedTasks]);

        $response->assertJsonMissing(['id' => $otherUserTask->id, 'name' => $otherUserTask->name]);
    }

    public function test_filter_tasks_by_status()
    {
        $this->setUpUser();

        $status_not_started = Status::factory(1)->create(['name' => 'Not Started', 'position' => 10])->first();
        $status_in_progress = Status::factory(1)->create(['name' => 'In Progress', 'position' => 20])->first();
        $status_completed = Status::factory(1)->create(['name' => 'Completed', 'position' => 30])->first();

        Task::factory(2)->create(['status_id' => $status_not_started->id]);
        Task::factory(4)->create(['status_id' => $status_in_progress->id]);
        Task::factory(8)->create(['status_id' => $status_completed->id]);
        // test status filtering

        $tasks_not_started = Task::where('status_id', $status_not_started->id)->orderBy('due_at', 'asc')->get();
        $tasks_in_progress = Task::where('status_id', $status_in_progress->id)->orderBy('due_at', 'asc')->get();
        $tasks_completed = Task::where('status_id', $status_completed->id)->orderBy('due_at', 'asc')->get();


        $expected = [
            'not_started' => [
                'meta' => [
                    'total' => $tasks_not_started->count()
                ],
                'data' => $tasks_not_started->map(function ($task) {

                    $due = json_decode((new DueAtResource($task))->toJson(), JSON_OBJECT_AS_ARRAY);

                    return [
                        'id' => $task->id,
                        'name' => $task->name,
                        'due_at' => $due,
                        'category' => [
                            'id' => $task->category->id,
                            'name' => $task->category->name
                        ],
                        'status' => [
                            'id' => $task->status->id,
                            'name' => $task->status->name
                        ]
                    ];
                })->toArray()
            ],
            'in_progress' => [
                'meta' => [
                    'total' => $tasks_in_progress->count()
                ],
                'data' => $tasks_in_progress->map(function ($task) {
                    $due = json_decode((new DueAtResource($task))->toJson(), JSON_OBJECT_AS_ARRAY);
                    return [
                        'id' => $task->id,
                        'name' => $task->name,
                        'due_at' => $due,
                        'category' => [
                            'id' => $task->category->id,
                            'name' => $task->category->name
                        ],
                        'status' => [
                            'id' => $task->status->id,
                            'name' => $task->status->name
                        ]
                    ];
                })->toArray()
            ],
            'completed' => [
                'meta' => [
                    'total' => $tasks_completed->count()
                ],
                'data' => $tasks_completed->map(function ($task) {
                    $due = json_decode((new DueAtResource($task))->toJson(), JSON_OBJECT_AS_ARRAY);
                    return [
                        'id' => $task->id,
                        'name' => $task->name,
                        'due_at' => $due,
                        'category' => [
                            'id' => $task->category->id,
                            'name' => $task->category->name
                        ],
                        'status' => [
                            'id' => $task->status->id,
                            'name' => $task->status->name
                        ]
                    ];
                })->toArray()
            ]
        ];

        $uri = route('api.task.list', ['filter[status]' => $status_not_started->id]);
        $response = $this->get($uri);
        $response->assertOk();
        $response->assertJson($expected['not_started']);

        $uri = route('api.task.list', ['filter[status]' => $status_in_progress->id]);
        $response = $this->get($uri);
        $response->assertOk();
        $response->assertJson($expected['in_progress']);

    }

    public function test_filter_tasks_by_category()
    {
        $this->setUpUser();

        $category1 = Category::factory(1)->create(['name' => 'Personal'])->first();
        $category2 = Category::factory(1)->create(['name' => 'Shopping'])->first();

        // create tasks
        Task::factory(2)->create(['category_id' => $category1->id]);
        Task::factory(5)->create(['category_id' => $category2->id]);

        $tasks = Task::where('category_id', $category2->id)->orderBy('due_at', 'asc')->get();

        $expected = [

            'meta' => [
                'total' => $tasks->count()
            ],
            'data' => $tasks->map(function ($task) {

                $due = json_decode((new DueAtResource($task))->toJson(), JSON_OBJECT_AS_ARRAY);

                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'due_at' => $due,
                    'category' => [
                        'id' => $task->category->id,
                        'name' => $task->category->name
                    ],
                    'status' => [
                        'id' => $task->status->id,
                        'name' => $task->status->name
                    ]
                ];
            })->toArray()
        ];

        $uri = route('api.task.list', ['filter[category]' => $category2->id]);
        $response = $this->get($uri);
        $response->assertOk();
        $response->assertJson($expected);
    }

    public function test_filter_tasks_by_status_and_category()
    {

        $this->setUpUser();

        $personal = Category::factory(1)->create(['name' => 'Personal'])->first();
        $shopping = Category::factory(1)->create(['name' => 'Shopping'])->first();

        $notStarted = Status::factory(1)->create(['name' => 'Not Started', 'position' => 10])->first();
        $completed = Status::factory(1)->create(['name' => 'Completed', 'position' => 30])->first();

        // create 2 tasks in Personal / Not Started
        Task::factory(2)->create(['category_id' => $personal->id, 'status_id' => $notStarted->id]);
        // create 3 tasks in Personal / Completed
        Task::factory(3)->create(['category_id' => $personal->id, 'status_id' => $completed->id]);

        //create 4 tasks in Shopping / Not started
        Task::factory(4)->create(['category_id' => $shopping->id, 'status_id' => $notStarted->id]);
        // create 1 task in Shopping / Completed
        Task::factory(1)->create(['category_id' => $shopping->id, 'status_id' => $completed->id]);

        // filter on Shopping / Not started - should get 4 results

        $tasks = Task::where('category_id', $shopping->id)
            ->where('status_id', $notStarted->id)
            ->orderBy('due_at')
            ->get();

        $expected = [
            'meta' => [
                'total' => $tasks->count()
            ],
            'data' => $tasks->map(function ($task) {

                $due = json_decode((new DueAtResource($task))->toJson(), JSON_OBJECT_AS_ARRAY);

                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'due_at' => $due,
                    'category' => [
                        'id' => $task->category->id,
                        'name' => $task->category->name
                    ],
                    'status' => [
                        'id' => $task->status->id,
                        'name' => $task->status->name
                    ]
                ];
            })->toArray()
        ];

        $uri = route('api.task.list', [
            'filter[category]' => $shopping->id,
            'filter[status]' => $notStarted->id,
        ]);

        $response = $this->get($uri);
        $response->assertOk();
        $response->assertJson($expected);

    }

    public function test_filter_tasks_by_due_date_range()
    {
        $this->setUpUser();

        // factory creates due dates -2 weeks ago and + 3weeks in future
        Task::factory(20)->create();

        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', now()->subDay(1)->format('Y-m-d 00:00:00'));
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', now()->addWeek(1)->format('Y-m-d 23:59:59'));

        // find tasks between yesterday and 1 week in future
        $tasks = Task::where('due_at', '>=', $startDate)
            ->where('due_at', '<=', $endDate)
            ->orderBy('due_at', 'asc')
            ->get();

        $expected = [
            'meta' => [
                'total' => $tasks->count()
            ],
            'data' => $tasks->map(function ($task) {

                $due = json_decode((new DueAtResource($task))->toJson(), JSON_OBJECT_AS_ARRAY);

                return [
                    'id' => $task->id,
                    'name' => $task->name,
                    'due_at' => $due,
                    'category' => [
                        'id' => $task->category->id,
                        'name' => $task->category->name
                    ],
                    'status' => [
                        'id' => $task->status->id,
                        'name' => $task->status->name
                    ]
                ];
            })->toArray()
        ];

//        dump('Start: '. $startDate->format('Y-m-d H:i:s') .' End: ' . $endDate->format('Y-m-d H:i:s'), $expected);

        $uri = route('api.task.list', [
            'filter[start]' => $startDate->timestamp,
            'filter[end]' => $endDate->timestamp,
        ]);

        $response = $this->get($uri);
        $response->assertOk();
        $response->assertJson($expected);

    }

    public function test_create_task_with_default_status_and_category()
    {
        $this->setUpUser();

        $category = Category::factory(1)->create(['name' => 'Shopping'])->first();
        $status = Status::factory(1)->create(['name' => 'Not Started'])->first();

        $dueDate = now()->addDay(1);

        $taskData = [
            'name' => 'Get Milk',
            'description' => 'Get 2 pints of whole milk and one skimmed',
            'due_at' => $dueDate->toIso8601String(),
            'duration' => 30
        ];

        $databaseHas = [
            'name' => $taskData['name'],
            'description' => $taskData['description'],
            'due_at' => $dueDate->format('Y-m-d H:i:s'),
            'duration' => $taskData['duration'],
            'category_id' => $category->id,
            'status_id' => $status->id,
            'user_id' => $this->user->id
        ];

        $uri = route('api.task.create');

        $response = $this->post($uri, $taskData);

        $response->assertCreated();
        $this->assertDatabaseHas('tasks', $databaseHas);
        $response->assertJson([
            'data' => [
                'name' => $databaseHas['name'],
                'description' => $databaseHas['description'],
                'status' => [
                    'id' => $status->id,
                    'name' => $status->name
                ],
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name
                ]
            ]
        ]);

    }

    public function test_create_task_with_status_and_category()
    {
        $this->setUpUser();

        $category = Category::factory(1)->create(['name' => 'Shopping'])->first();
        $status = Status::factory(1)->create(['name' => 'Not Started'])->first();

        $dueDate = now()->addDay(1);

        $taskData = [
            'name' => 'Build the shed',
            'description' => 'Put the new shed up',
            'due_at' => $dueDate->toIso8601String(),
            'duration' => 360,
            'status_id' => $status->id,
            'category_id' => $category->id
        ];

        $databaseHas = [
            'name' => $taskData['name'],
            'description' => $taskData['description'],
            'due_at' => $dueDate->format('Y-m-d H:i:s'),
            'duration' => $taskData['duration'],
            'category_id' => $category->id,
            'status_id' => $status->id,
            'user_id' => $this->user->id
        ];

        $uri = route('api.task.create');

        $response = $this->post($uri, $taskData);

        $response->assertCreated();
        $this->assertDatabaseHas('tasks', $databaseHas);
        $response->assertJson([
            'data' => [
                'name' => $databaseHas['name'],
                'description' => $databaseHas['description'],
                'status' => [
                    'id' => $status->id,
                    'name' => $status->name
                ],
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name
                ]
            ]
        ]);

    }

    public function test_update_task()
    {
        $this->setUpUser();

        $task = Task::factory(1)->create()->first();

        // update title, description, and other text fields
        $this->assertDatabaseHas('tasks', [
            'name' => $task->name,
            'description' => $task->description,
            'duration' => $task->duration,
            'due_at' => $task->due_at,
            'url' => $task->url
        ]);

        $updateData = [
            'name' => 'This is an updated task name',
            'description' => 'This is an updated task description',
            'duration' => '40',
            'due_at' => now()->addWeek(2),
            'url' => 'https://www.example.net'
        ];

        $uri = route('api.task.update', $task);

        $response = $this->patch($uri, $updateData);



        $updatedData = array_merge(['id' => $task->id], $updateData);

        unset($updatedData['due_at']);

        $response->assertOk()
            ->assertJson(['data' => $updatedData]);

        $this->assertDatabaseHas('tasks', $updatedData);

        // update category

        // update status


    }

    public function test_delete_task()
    {
        $this->setUpUser();

        $task = Task::factory(1)->create()->first();

        $uri = route('api.task.delete', $task);

        $this->delete($uri)
            ->assertNoContent();

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);

    }


}

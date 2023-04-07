<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Status;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fails_creating_status_without_valid_user()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing authenticated user');

        $data = [
            'name' => 'Test',
            'description' => 'Testing the task description',
            'notes' => '## Test note',
            'due_at' => '2023-06-01 09:00:00',
            'duration' => 30
        ];
        Task::create($data);
    }

    public function test_it_creates_task_with_uuid_and_user(): void
    {

        $user = $this->user();
        $this->actingAs($user);

        $data = [
            'name' => 'Test',
            'description' => 'Testing the task description',
            'notes' => '## Test note',
            'due_at' => '2023-06-01 09:00:00',
            'duration' => 30
        ];

        $task = Task::create($data);

        $this->assertSame($task->user_id, $user->id);
        $this->assertSame($task->name, $data['name']);
        $this->assertSame($task->description, $data['description']);
        $this->assertSame($task->notes, $data['notes']);
        $this->assertSame($task->due_at, $data['due_at']);
        $this->assertSame($task->duration, $data['duration']);
        $this->assertSame($task->user->name, $user->name);
    }

    public function test_task_has_category()
    {
        $user = $this->user();
        $this->actingAs($user);

        $categoryData = ['name' => 'Test', 'description' => 'This is a test', 'position' => 1];
        $category = Category::create($categoryData);

        $data = [
            'name' => 'Test',
            'description' => 'Testing the task description',
            'notes' => '## Test note',
            'due_at' => '2023-06-01 09:00:00',
            'duration' => 30
        ];

        /** @var Task $task */
        $task = Task::create($data);

        $task->category()->associate($category);

        $this->assertSame($task->category->id, $category->id);
        $this->assertSame($task->category->name, $category->name);

    }

    public function test_task_has_status()
    {
        $user = $this->user();
        $this->actingAs($user);

        $statusData = ['name' => 'In Progress', 'position' => 2];
        $status = Status::create($statusData);

        $data = [
            'name' => 'Test',
            'description' => 'Testing the task description',
            'notes' => '## Test note',
            'due_at' => '2023-06-01 09:00:00',
            'duration' => 30
        ];

        /** @var Task $task */
        $task = Task::create($data);

        $task->status()->associate($status);

        $this->assertSame($task->status->id, $status->id);
        $this->assertSame($task->status->name, $status->name);

    }

    public function test_task_has_status_and_category()
    {
        $user = $this->user();
        $this->actingAs($user);

        $statusData = ['name' => 'In Progress', 'position' => 2];
        $status = Status::create($statusData);

        $categoryData = ['name' => 'Test', 'description' => 'This is a test', 'position' => 1];
        $category = Category::create($categoryData);

        $data = [
            'name' => 'Test',
            'description' => 'Testing the task description',
            'notes' => '## Test note',
            'due_at' => '2023-06-01 09:00:00',
            'duration' => 30
        ];

        /** @var Task $task */
        $task = Task::create($data);

        $task->status()->associate($status);
        $task->category()->associate($category);

        $this->assertSame($task->status->id, $status->id);
        $this->assertSame($task->status->name, $status->name);
        $this->assertSame($task->category->id, $category->id);
        $this->assertSame($task->category->name, $category->name);

    }

}

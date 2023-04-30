<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Category;
use App\Models\Status;
use App\Models\Task;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TaskController extends Controller
{
    public function show(Task $task)
    {
        if (auth()->user()->id !== $task->user_id) {
            throw new UnauthorizedHttpException('Access Denied');
        }

        return new TaskResource($task);
    }

    public function index()
    {
        if (!auth()->check()) {
            throw new UnauthorizedHttpException('Access Denied');
        }

        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters([
                AllowedFilter::exact('status', 'status_id'),
                AllowedFilter::exact('category', 'category_id'),
                AllowedFilter::scope('start', 'due_after'),
                AllowedFilter::scope('end', 'due_before'),
//                AllowedFilter::scope('due', 'due_on'),

            ])
            ->orderBy('due_at', 'asc')
            ->paginate()
            ->appends(request()->query());

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();

        if (empty($data['category_id'])) {
            // get the default category
            $category = Category::orderBy('position', 'asc')->first();
        } else {
            $category = Category::findOrFail($data['category_id']);
        }

        if (empty($data['status_id'])) {
            // get the default status
            $status = Status::orderBy('position', 'asc')->first();
        } else {
            $status = Status::findOrFail($data['status_id']);
        }

        unset($data['status_id'], $data['category_id']);

        $task = Task::create($data);
        $category->tasks()->save($task);
        $status->tasks()->save($task);

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        return new TaskResource($task);
    }


    public function destroy(Task $task)
    {
        $task->delete();
        return response(null, Response::HTTP_NO_CONTENT);

    }
}

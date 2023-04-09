<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\StatusCollectionResource;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class StatusController extends Controller
{
    /**
     * List statuses
     */
    public function index()
    {
        $statuses = Status::where('user_id', auth()->user()->id)
            ->orderBy('position')
            ->select('id', 'name', 'position')
            ->get();

        return new StatusCollectionResource($statuses);
    }

    /**
     * Store a new status
     */
    public function store(StoreStatusRequest $request)
    {
        $data = $request->validated();
        // update all status positions to accommodate this one
        Status::where('user_id', auth()->user()->id)
            ->where('position', '>=', $data['position'])
            ->increment('position');
        // now create this one
        $status = Status::create($data);

        return new StatusResource($status);
    }

    /**
     * Get a single status
     */
    public function show(Status $status)
    {
        // authorize this
        if (auth()->user()->id !== $status->user_id) {
            throw new UnauthorizedHttpException('Access Denied');
        }
        return new StatusResource($status);
    }

    /**
     * Update the status.
     */
    public function update(UpdateStatusRequest $request, Status $status)
    {
        $data = $request->validated();
        if ($data['position'] != $status->position) {
            // update all other positions
            Status::where('user_id', auth()->user()->id)
            ->where('position', '>=', $data['position'])
            ->increment('position');
        }

        $status->update($data);
        return new StatusResource($status);
    }

    /**
     * Delete the status
     */
    public function destroy(Status $status)
    {
        // authorize this
        if (auth()->user()->id !== $status->user_id) {
            throw new UnauthorizedHttpException('Access Denied');
        }
        $status->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}

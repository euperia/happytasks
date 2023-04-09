<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\StatusCollectionResource;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
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
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        // authorize this?
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
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        dd($status->toArray());
    }
}

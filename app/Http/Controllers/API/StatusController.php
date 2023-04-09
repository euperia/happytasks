<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStatusRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Resources\StatusResource;
use App\Models\Status;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        dd($status->toArray());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStatusRequest $request, Status $status)
    {
        dd($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        dd($status->toArray());
    }
}

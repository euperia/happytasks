<?php

namespace App\Listeners;

use App\Models\Category;
use App\Models\Status;
use App\Events\NewRegistration;

class NewRegistrationCategoryAndStatuses
{
    /**
     * Handle the event.
     */
    public function handle(NewRegistration $event): void
    {
        collect(config('app.defaults.categories'))->each(function($category) use ($event) {
            $category = Category::make([
                'name' => $category['name'],
                'position' => $category['position'],
            ]);
            $category->user_id = $event->user->id;
            $category->save();
        });

        collect(config('app.defaults.statuses'))->each(function($status) use ($event) {
            $status = Status::make([
                'name' => $status['name'],
                'position' => $status['position']
            ]);
            $status->user_id = $event->user->id;
            $status->save();
        });
    }
}

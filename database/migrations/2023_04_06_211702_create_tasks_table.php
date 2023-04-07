<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->nullable(false);
            $table->foreignUuid('category_id')->nullable()->constrained('categories');
            $table->foreignUuid('status_id')->nullable()->constrained('statuses');
            $table->string('name')->nullable(false);
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->unsignedTinyInteger('duration')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

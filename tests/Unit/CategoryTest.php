<?php

namespace Tests\Unit;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fails_creating_category_without_valid_user()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing authenticated user');

        $data = ['name' => 'Test', 'description' => 'This is a test', 'position' => 1];
        $category = Category::create($data);
    }

    public function test_it_creates_category_with_uuid_and_user(): void
    {


        $this->setUpUser();

        $data = ['name' => 'Test', 'description' => 'This is a test', 'position' => 1];
        $category = Category::create($data);

        $this->assertSame($category->user_id, $this->user->id);
        $this->assertSame($category->name, $data['name']);
        $this->assertSame($category->description, $data['description']);
        $this->assertSame($category->position, $data['position']);
    }
}

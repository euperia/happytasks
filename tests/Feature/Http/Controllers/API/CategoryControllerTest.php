<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{

    use RefreshDatabase;

    public function testStatusAuthFail()
    {
        $url = route('api.category.get', ['category' => '1ba5e529-b47f-4124-afe1-4bd6cdd4c969']);
        $response = $this->get($url);
        $this->assertSame(302, $response->status());
    }

    public function test_get_a_category()
    {
        $this->setUpUser();

        // add three new statuses so that we can check the position update
        DB::table('categories')->insert([
            [
                'id' => (string) Str::uuid(),
                'name' => 'Category 1',
                'description' => 'Description of category 1',
                'position' => 1,
                'user_id' => $this->user->id, 'created_at' => now()
            ]
        ]);

        $model = Category::first();

        $uri = route('api.category.get', [$model]);

        $response = $this->getJson($uri);

        $response->assertOk();
        $response->assertJson([
            'id' => $model->id,
            'parent_id' => null,
            'name' => $model->name,
            'description' => $model->description,
            'position' => $model->position,
        ]);
    }

    /**
     * Test get category list
     * Include sub categories recursive
     */
    public function test_get_category_list()
    {
       $this->setUpUser();

        // create 5 categories
        $categories = Category::factory(5)->create();

        $uri = route('api.category.list');
        $response = $this->getJson($uri);
        $response->assertOk();

        foreach($categories as $category) {
            $response->assertJsonFragment($category->only('name', 'description', 'position'));
        }

    }


    /**
     * Test create a category
     * Don't allow duplicate names with same parent_id
     */
    public function test_it_creates_a_category()
    {
        $this->setUpUser();

        $data = [
            'name' => 'AE Smith & Son',
            'description' => 'Category to hold all of AE Smith\'s tasks'
        ];

        $uri = route('api.category.create');

        $response = $this->post($uri, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'name' => $data['name'],
            'description' => $data['description']
        ]);

    }

    public function test_it_creates_a_child_category()
    {
        $this->setUpUser();


        $parent = Category::create([
            'name' => 'Parent Category',
            'description' => 'Category to hold all of the children'
        ]);

        $child = [
            'name' => 'Child Category',
            'description' => 'I am the child',
            'parent_id' => $parent->id
        ];
        $uri = route('api.category.create');

        $response = $this->post($uri, $child);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'name' => $child['name'],
            'description' => $child['description'],
            'parent_id' => $parent->id
        ]);

    }



    /**
     * Test update a category
     * Don't allow duplicate names with same parent_id
     */
    public function test_it_updates_a_category()
    {
       $this->setUpUser();

        $description = 'Testing the update - this should be the same after';
        $category = Category::create([
            'name' => 'Parent Category',
            'description' => $description,
            'position' => 2
        ]);

        $this->assertSame($category->position, 2);

        $uri = route('api.category.update', $category);

        $updateData = [
            'name' => 'Updated Parent Category',
            'position' => 1
        ];
        $response = $this->put($uri, $updateData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'name' => $updateData['name'],
            'description' => $description,
            'position' => $updateData['position']
        ]);

    }

    public function test_it_updates_a_category_parent()
    {
        $this->setUpUser();

        $category1 = Category::create([
            'name' => 'Parent Category 1',
            'position' => 1
        ]);
        $category2 = Category::create([
            'name' => 'Parent Category 2',
            'position' => 2
        ]);

        $category3 = Category::create([
            'name' => 'Child 1 Category',
            'position' => 1,
            'parent_id' => $category1->id
        ]);

        $uri = route('api.category.update', $category3);

        $updateData = [
            'name' => 'Updated Child Category',
            'parent_id' => $category2->id
        ];

        $response = $this->put($uri, $updateData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'name' => $updateData['name'],
            'parent_id' => $category2->id
        ]);

    }

    /**
     * Test delete a category
     * if a category has tasks, cannot delete the category
     */
    public function test_category_with_tasks_cannot_be_deleted()
    {
        $this->setUpUser();

        $category = Category::create([
            'name' => 'Parent Category 1',
            'position' => 1
        ]);

        Task::factory(2)->create(['category_id' => $category->id]);

        $uri = route('api.category.delete', $category);

        $response = $this->delete($uri);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['message' => 'Cannot delete - has active tasks']);

    }

    public function test_category_can_be_deleted()
    {
        $this->setUpUser();

        $category = Category::create([
            'name' => 'Parent Category 1',
            'position' => 1
        ]);

        $uri = route('api.category.delete', $category);

        $response = $this->delete($uri);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['message' => 'Deleted OK']);

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }
}

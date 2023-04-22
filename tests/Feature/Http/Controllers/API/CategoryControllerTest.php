<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
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
        $user = User::factory()->create();

        // add three new statuses so that we can check the position update
        DB::table('categories')->insert([
            [
                'id' => (string) Str::uuid(),
                'name' => 'Category 1',
                'description' => 'Description of category 1',
                'position' => 1,
                'user_id' => $user->id, 'created_at' => now()
            ]
        ]);

        $model = Category::first();

        $uri = route('api.category.get', [$model]);

        Sanctum::actingAs($user, ['*']);
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
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

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
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

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
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);


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
     * @todo test update a category
     * Don't allow duplicate names with same parent_id
     */

    /**
     * @todo Test delete a category
     * Only if no tasks assigned
     */
}

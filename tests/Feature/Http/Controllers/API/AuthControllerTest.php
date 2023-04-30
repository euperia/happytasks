<?php

namespace Tests\Feature\Http\Controllers\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{

    use RefreshDatabase;

    public function test_registration_validation(): void
    {
        // Test empty name validation
        // test empty email validation
        // test invalid email validation
        // test password rules validation
        // test passwords match validation
        $formData = [
            'name' => '',
            'email'=> '',
            'password' => '',
            'password_confirmation' => ''
        ];

        $response = $this->post(route('api.register', $formData));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonFragment(['email' => ['The email field is required.']]);
        $response->assertJsonFragment(['name' => ['The name field is required.']]);
        $response->assertJsonFragment(['password' => ['The password field is required.']]);

        // Validate email
        $formData = [
            'name' => 'Bob Smith',
            'email'=> '',
            'password' => '',
            'password_confirmation' => ''
        ];

        $response = $this->post(route('api.register', $formData));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonFragment(['email' => ['The email field is required.']]);
        $response->assertJsonFragment(['password' => ['The password field is required.']]);

        // Validate email
        $formData = [
            'name' => 'Bob Smith',
            'email'=> 'test',
            'password' => '',
            'password_confirmation' => ''
        ];

        $response = $this->post(route('api.register', $formData));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonFragment(['email' => ['The email field must be a valid email address.']]);
        $response->assertJsonFragment(['password' => ['The password field is required.']]);

        // Validate password
        $formData = [
            'name' => 'Bob Smith',
            'email'=> 'bob@example.net',
            'password' => '123',
            'password_confirmation' => ''
        ];

        $response = $this->post(route('api.register', $formData));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonFragment(['password' => [
            "The password field confirmation does not match.",
            "The password field must be at least 8 characters.",
            "The password field must contain at least one letter.",
            "The password field must contain at least one symbol.",
            "The password field must contain at least one uppercase and one lowercase letter."
        ]]);

        $formData = [
            'name' => 'Bob Smith',
            'email'=> 'bob@example.net',
            'password' => '12345678901234567',
            'password_confirmation' => ''
        ];

        $response = $this->post(route('api.register', $formData));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonFragment(['password' => [
            "The password field confirmation does not match.",
            "The password field must contain at least one letter.",
            "The password field must contain at least one symbol.",
            "The password field must contain at least one uppercase and one lowercase letter."
        ]]);
    }

    public function test_registration_success()
    {
        $formData = [
            'name' => 'Bob Smith',
            'email'=> 'bob@example.net',
            'password' => 'This%Is^4-pa33word',
            'password_confirmation' => 'This%Is^4-pa33word'
        ];

        $response = $this->post(route('api.register', $formData));
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson([
            'data' => [
                'name' => $formData['name'],
                'email' => $formData['email'],
            ],
            'token_type' => 'Bearer',
        ]);

        // check the database
        $user = User::where('email', $formData['email'])->first();
        $this->assertNotNull($user);
        $this->assertInstanceOf(User::class, $user);

        // check the user default categories and statuses have been created
        $this->assertSame(config('app.defaults.categories')[0]['name'], $user->categories[0]->name);
        $this->assertSame(config('app.defaults.statuses')[0]['name'], $user->statuses[0]->name);
        $this->assertSame(config('app.defaults.statuses')[1]['name'], $user->statuses[1]->name);
    }


    public function test_login() {

        $userData = [
            'name' => 'Bob Smith',
            'email' => 'bob@example.net',
            'password' => 'This$is%A-t35t'
        ];

         $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password'])
         ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $formData = [
            'email' => $userData['email'],
            'password' => $userData['password']
        ];

        $response = $this->post(route('api.login'), $formData);

        $response->assertOk();
        $response->assertJson(['message' => 'Hi ' . $userData['name'] . ', welcome to home']);
        $response->assertJson(['token_type' => 'Bearer']);

        $responseData = json_decode($response->content(), JSON_OBJECT_AS_ARRAY);

        $this->assertTrue(isset($responseData['access_token']));
        $this->assertFalse(empty($responseData['access_token']));

    }


    public function test_logout()
    {
        $userData = [
            'name' => 'Bob Smith',
            'email' => 'bob@example.net',
            'password' => 'This$is%A-t35t'
        ];

         $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password'])
         ]);

        $user->createToken('auth_token');

        $this->actingAs($user);


        $response = $this->post(route('api.logout'));

        $response->assertJson([
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ]);
    }

}

<?php

namespace Tests\Feature\Http\Controllers\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
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
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

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
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

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
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

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
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

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
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

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
        $response->assertStatus(ResponseAlias::HTTP_OK);


    }


}

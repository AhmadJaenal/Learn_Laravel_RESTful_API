<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'first_name' => 'Ahmad',
            'last_name'  => 'Jenal',
            'email' => 'Jenal@gmail.com',
            'phone' => '1245676543',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'first_name' => 'Ahmad',
                    'last_name'  => 'Jenal',
                    'email' => 'Jenal@gmail.com',
                    'phone' => '1245676543',
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name'  => 'Jenal',
            'email' => 'Jenalgmail.com',
            'phone' => '1245676543',
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'first_name' => [
                        "The first name field is required."
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ],
                ]
            ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name'  => 'Jenal',
            'email' => 'Jenalgmail.com',
            'phone' => '1245676543',
        ], [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    'message' => [
                        "unauthorized"
                    ],
                ]
            ]);
    }
}

<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsersAdministrationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function unauthenticated_users_cannot_access()
    {
        $this->get('/frontend/user')
            ->assertJson(['error' => 'token_not_provided'])
            ->assertStatus(400);
    }

    /** @test */
    public function display_all_users()
    {
        $this->signIn();

        $user = factory(User::class, 3)->create();

        $response = $this->get('/frontend/users')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name', 'sex', 'email', 'age']]])
            ->assertStatus(200);
    }

    /** @test */
    public function users_can_be_filtered_by_name()
    {
        $user = factory(User::class)->create(['name' => 'rodrigore']);

        $this->signIn($user);

        $response = $this->get('/frontend/users?name=rodri')->decodeResponseJson();
        $this->assertCount(1, $response['data']);
    }

    /** @test */
    public function users_can_be_filtered_by_sex()
    {
        factory(User::class)->create(['sex' => $female = 0]);
        $user = factory(User::class)->create(['sex' => $male = 1]);

        $this->signIn($user);

        $response = $this->get('/frontend/users?sex=1')->decodeResponseJson();
        $this->assertCount(1, $response['data']);

        $response = $this->get('/frontend/users?sex=0')->decodeResponseJson();
        $this->assertCount(1, $response['data']);
    }
}

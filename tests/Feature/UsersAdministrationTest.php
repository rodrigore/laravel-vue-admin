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
        $this->signIn();
        factory(User::class)->create(['name' => 'rodrigore']);

        $response = $this->get('/frontend/users?name=rodri')->json();
        $this->assertCount(1, $response['data']);
    }

    /** @test */
    public function users_can_be_filtered_by_sex()
    {
        factory(User::class)->create(['sex' => $female = 0]);
        $user = factory(User::class)->create(['sex' => $male = 1]);
        $this->signIn($user);

        $response = $this->get('/frontend/users?sex=1')->json();
        $this->assertCount(1, $response['data']);

        $response = $this->get('/frontend/users?sex=0')->json();
        $this->assertCount(1, $response['data']);
    }

    /** @test */
    public function users_can_be_sorted_by_fields()
    {
        factory(User::class)->create([
            'name' => 'alan',
            'email' => 'some@email.com',
            'age' => 18,
            'birth' => '1998-01-01',
            'address' => 'super street'
        ]);
        $user = factory(User::class)->create([
            'name' => 'rodrigore',
            'email' => 'other@email.com',
            'age' => 24,
            'birth' => '2004-01-01',
            'address' => '22 acacia avenue'
        ]);
        $this->signIn($user);

        // sort by name
        $response = $this->get('/frontend/users?sortBy=name,asc')->json();
        $this->assertEquals('alan', $response['data'][0]['name']);
        $this->assertEquals('rodrigore', $response['data'][1]['name']);

        $response = $this->get('/frontend/users?sortBy=name,desc')->json();
        $this->assertEquals('rodrigore', $response['data'][0]['name']);
        $this->assertEquals('alan', $response['data'][1]['name']);

        // sort by email
        $response = $this->get('/frontend/users?sortBy=email,asc')->json();
        $this->assertEquals('other@email.com', $response['data'][0]['email']);
        $this->assertEquals('some@email.com', $response['data'][1]['email']);

        $response = $this->get('/frontend/users?sortBy=email,desc')->json();
        $this->assertEquals('some@email.com', $response['data'][0]['email']);
        $this->assertEquals('other@email.com', $response['data'][1]['email']);

        // sort by age
        $response = $this->get('/frontend/users?sortBy=age,asc')->json();
        $this->assertEquals(18, $response['data'][0]['age']);
        $this->assertEquals(24, $response['data'][1]['age']);

        $response = $this->get('/frontend/users?sortBy=age,desc')->json();
        $this->assertEquals(24, $response['data'][0]['age']);
        $this->assertEquals(18, $response['data'][1]['age']);

        // sort by birth
        $response = $this->get('/frontend/users?sortBy=birth,asc')->json();
        $this->assertEquals('1998-01-01', $response['data'][0]['birth']);
        $this->assertEquals('2004-01-01', $response['data'][1]['birth']);

        $response = $this->get('/frontend/users?sortBy=birth,desc')->json();
        $this->assertEquals('2004-01-01', $response['data'][0]['birth']);
        $this->assertEquals('1998-01-01', $response['data'][1]['birth']);

        // sort by address
        $response = $this->get('/frontend/users?sortBy=address,asc')->json();
        $this->assertEquals('22 acacia avenue', $response['data'][0]['address']);
        $this->assertEquals('super street', $response['data'][1]['address']);

        $response = $this->get('/frontend/users?sortBy=address,desc')->json();
        $this->assertEquals('super street', $response['data'][0]['address']);
        $this->assertEquals('22 acacia avenue', $response['data'][1]['address']);
    }
}

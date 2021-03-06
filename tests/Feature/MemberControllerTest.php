<?php

namespace Tests\Feature;

use App\Member;
use App\Officer;
use App\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemberControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Runs tests to check if authentication works.
     *
     * This test simply checks if an unauthenticated request is redirected to the
     * login form, ensuring that the request should be going through the authentication
     * middleware.
     *
     * This test, and the authorization test, assume that authenticated users are
     * checked via the other tests for functionality.
     *
     * @return void
     */
    public function testAuthentication()
    {
        $response = $this->get('/members');

        $response->assertRedirect('/login');
    }

    /**
     * Runs tests to check if authorization works.
     *
     * This test runs by checking to see if all unauthorized requests against
     * our endpoints return a 403 status code. Theoretically, a 403 response code
     * means that the policy is being applied. The policy logic is tested in the
     * MemberPolicyTest unit test.
     *
     * @return void
     */
    public function testAuthorization()
    {
        $member = factory(Member::class)->create();
        $member2 = factory(Member::class)->create();
        $user = factory(User::class)->create([
            'member_id' => $member->id
        ]);

        $response = $this->actingAs($user)->get('/members/create');
        $response2 = $this->actingAs($user)->post('/members');
        $response3 = $this->actingAs($user)->get('/members/' . $member2->id . '/edit');
        $response4 = $this->actingAs($user)->put('/members/' . $member2->id);
        $response5 = $this->actingAs($user)->delete('/members/' . $member2->id);

        $response->assertStatus(403);
        $response2->assertStatus(403);
        $response3->assertStatus(403);
        $response4->assertStatus(403);
        $response5->assertStatus(403);
    }

    public function testIndex()
    {
        $member = factory(Member::class)->create();
        $user = factory(User::class)->create([
            'member_id' => $member->id
        ]);

        $request = $this->actingAs($user)->get('/members');
        $request->assertStatus(200);
        $request->assertViewHas('members');
    }

    public function testCreate()
    {
        $member = factory(Member::class)->create();
        $officer = factory(Officer::class)->create([
            'member_id' => $member->id
        ]);
        $user = factory(User::class)->create([
            'member_id' => $member->id
        ]);

        $request = $this->actingAs($user)->get('/members/create');
        $request->assertStatus(200);
    }

    public function testStore()
    {
        $member = factory(Member::class)->create();
        $user = factory(User::class)->create([
            'member_id' => $member->id
        ]);
        factory(Officer::class)->create([
            'member_id' => $member->id
        ]);

        // Test empty request.
        $request = $this->actingAs($user)->post('/members');
        $request->assertStatus(302);

        // Test proper request.
        $faker = \Faker\Factory::create();
        $formData = [
            'id' => $faker->randomNumber,
            'first' => $faker->firstName,
            'last' => $faker->lastName,
            'email' => $faker->safeEmail,
            'phone' => $faker->phoneNumber,
            'graduation' => $faker->year
        ];
        $request2 = $this->actingAs($user)->post('/members', $formData);
        $request2->assertStatus(200);
        $request2->assertViewIs('member.index');
        $request2->assertViewHas('status')->assertViewHas('member')->assertViewHas('members');
    }

    public function testShow()
    {
        $member = factory(Member::class)->create();
        $user = factory(User::class)->create([
            'member_id' => $member->id
        ]);
        $member2 = factory(Member::class)->create();

        // Test request against the same user.
        $request = $this->actingAs($user)->get('/members/' . $member->id);
        $request->assertStatus(200);
        $request->assertViewIs('member.show');
        $request->assertViewHas('member');
    }

    public function testEdit()
    {
        $member = factory(Member::class)->create();
        $user = factory(User::class)->create([
            'member_id' => $member->id
        ]);
        factory(Officer::class)->create([
            'member_id' => $member->id
        ]);

        // Test request against the same user.
        $request = $this->actingAs($user)->get('/members/' . $member->id . '/edit');
        $request->assertStatus(200);
        $request->assertViewHas('member');
    }

    public function testUpdate()
    {
        $member = factory(Member::class)->create();
        $user = factory(User::class)->create([
            'member_id' => $member->id
        ]);
        factory(Officer::class)->create([
            'member_id' => $member->id
        ]);

        // Test empty request.
        $request = $this->actingAs($user)->put('/members/' . $member->id);
        $request->assertStatus(302);

        $faker = \Faker\Factory::create();
        $form = [
            'id' => $member->id,
            'first' => $faker->firstName,
            'last' => $faker->lastName,
            'email' => $faker->safeEmail,
            'phone' => $faker->phoneNumber,
            'graduation' =>$faker->year
        ];

        // Test proper request.
        $request2 = $this->actingAs($user)->put('/members/' . $member->id, $form);
        $request2->assertStatus(200);
        $request2->assertViewIs('member.show');
        $this->assertDatabaseHas('members', $form);
    }

    public function testDelete()
    {
        $member = factory(Member::class)->create();
        $member2 = factory(Member::class)->create();
        $user = factory(User::class)->create([
            'member_id' => $member->id
        ]);
        factory(Officer::class)->create([
            'member_id' => $member->id
        ]);

        $request = $this->actingAs($user)->delete('/members/' . $member->id);
        $request->assertStatus(302);
        $request->assertSessionHas('status', 403);
        $request->assertSessionHas('type', 'self');

        $request2 = $this->actingAs($user)->delete('/members/' . $member2->id);
        $request2->assertStatus(302);
        $request2->assertSessionHas('status', 200);
        $request2->assertSessionHas('type', 'delete');
        $this->assertDatabaseMissing('members', [
            'id' => $member2->id
        ]);
    }
}

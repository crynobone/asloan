<?php

namespace Tests\Feature\Stories;

use App\Loan;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class UserCanApplyLoanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_apply_loan()
    {
        TestTime::freeze();

        $now = Carbon::now();
        $termEndedAt = $now->copy()->addDays(7);
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $response = $this->post('/apply-loan', [
            'description' => 'Request Loan for Home Renovation',
            'total' => '30000.50',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->format('Y-m-d'),
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('status', 'Successfully apply loan SGD 30000.50');

        $this->assertDatabaseHas('loans', [
            'user_id' => "{$user->getKey()}",
            'amount' => '3000050',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->copy()->startOfDay()->toDatetimeString(),
        ]);
    }

    /** @test */
    public function user_can_apply_more_than_one_loan()
    {
        TestTime::freeze();

        $now = Carbon::now();
        $termEndedAt = $now->copy()->addDays(7);
        $user = factory(User::class)->create();

        $loan = factory(Loan::class)->create([
            'user_id' => $user->getKey(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/apply-loan', [
            'description' => 'Request Loan for Home Renovation',
            'total' => '30000.50',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->format('Y-m-d'),
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('status', 'Successfully apply loan SGD 30000.50');

        $this->assertDatabaseHas('loans', [
            'user_id' => "{$user->getKey()}",
            'amount' => '3000050',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->copy()->startOfDay()->toDatetimeString(),
        ]);
    }

    /** @test */
    public function user_cant_apply_loan_with_zero_amount()
    {
        TestTime::freeze();

        $now = Carbon::now();
        $termEndedAt = $now->copy()->addDays(7);
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $response = $this->post('/apply-loan', [
            'description' => 'Request Loan for Home Renovation',
            'total' => '0.00',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->format('Y-m-d'),
        ]);

        $response->assertRedirect('/')
            ->assertSessionMissing('status', 'Successfully apply loan SGD 30000.50');

        $this->assertDatabaseMissing('loans', [
            'user_id' => "{$user->getKey()}",
            'amount' => '0',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->copy()->startOfDay()->toDatetimeString(),
        ]);
    }

    /** @test */
    public function user_cant_apply_loan_with_negative_amount()
    {
        TestTime::freeze();

        $now = Carbon::now();
        $termEndedAt = $now->copy()->addDays(7);
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $response = $this->post('/apply-loan', [
            'description' => 'Request Loan for Home Renovation',
            'total' => '-350.00',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->format('Y-m-d'),
        ]);

        $response->assertRedirect('/')
            ->assertSessionMissing('status', 'Successfully apply loan SGD 30000.50');

        $this->assertDatabaseMissing('loans', [
            'user_id' => "{$user->getKey()}",
            'amount' => '-35000',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->copy()->startOfDay()->toDatetimeString(),
        ]);
    }

    /** @test */
    public function user_cant_apply_loan_term_expiry_less_than_tomorrow()
    {
        TestTime::freeze();

        $now = Carbon::now();
        $termEndedAt = $now->copy();
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $response = $this->post('/apply-loan', [
            'description' => 'Request Loan for Home Renovation',
            'total' => '3500.00',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->format('Y-m-d'),
        ]);

        $response->assertRedirect('/')
            ->assertSessionMissing('status', 'Successfully apply loan SGD 30000.50');

        $this->assertDatabaseMissing('loans', [
            'user_id' => "{$user->getKey()}",
            'amount' => '350000',
            'currency' => 'SGD',
            'term_ended_at' => $termEndedAt->copy()->startOfDay()->toDatetimeString(),
        ]);
    }
}

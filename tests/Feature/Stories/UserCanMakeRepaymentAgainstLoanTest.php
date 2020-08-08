<?php

namespace Tests\Feature\Stories;

use App\Loan;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class UserCanMakeRepaymentAgainstLoanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_make_repayment_to_a_loan()
    {
        TestTime::freeze();

        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->getKey(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/make-payment/'.$loan->getKey(), [
            'total' => '10.00',
            'currency' => 'SGD',
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('status', 'Successfully make loan payment SGD 10.00');

        $this->assertDatabaseHas('repayments', [
            'loan_id' => "{$loan->getKey()}",
            'amount' => '1000',
            'currency' => 'SGD',
            'occured_at' => Carbon::now()->toDatetimeString(),
        ]);
    }

    /** @test */
    public function user_cant_make_repayment_to_a_loan_with_zero_amount()
    {
        TestTime::freeze();

        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->getKey(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/make-payment/'.$loan->getKey(), [
            'total' => '0.00',
            'currency' => 'SGD',
        ]);

        $response->assertRedirect('/')
            ->assertSessionMissing('status', 'Successfully make loan payment SGD 10.00');

        $this->assertDatabaseMissing('repayments', [
            'loan_id' => "{$loan->getKey()}",
            'amount' => '0',
            'currency' => 'SGD',
            'occured_at' => Carbon::now()->toDatetimeString(),
        ]);
    }

    /** @test */
    public function user_cant_make_repayment_to_a_loan_using_different_currency()
    {
        TestTime::freeze();

        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->getKey(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/make-payment/'.$loan->getKey(), [
            'total' => '10.00',
            'currency' => 'USD',
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('error', 'Unable to make payment with other currency than SGD');

        $this->assertDatabaseMissing('repayments', [
            'loan_id' => "{$loan->getKey()}",
            'amount' => '10.00',
            'currency' => 'USD',
            'occured_at' => Carbon::now()->toDatetimeString(),
        ]);
    }

    /** @test */
    public function user_cant_make_repayment_to_a_completed_loan()
    {
        TestTime::freeze();

        $user = factory(User::class)->create();
        $loan = factory(Loan::class)->create([
            'user_id' => $user->getKey(),
            'amount' => 400000,
            'currency' => 'SGD',
            'completed_at' => Carbon::now(),
        ]);

        $this->actingAs($user);

        $response = $this->post('/make-payment/'.$loan->getKey(), [
            'total' => '10.00',
            'currency' => 'SGD',
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('error', 'Unable to make payment against completed loan');

        $this->assertDatabaseMissing('repayments', [
            'loan_id' => "{$loan->getKey()}",
            'amount' => '10.00',
            'currency' => 'USD',
            'occured_at' => Carbon::now()->toDatetimeString(),
        ]);
    }
}

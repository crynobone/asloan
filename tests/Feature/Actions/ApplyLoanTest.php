<?php

namespace Tests\Feature\Actions;

use App\Actions\ApplyLoan;
use App\Loan;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Money\Currency;
use Money\Money;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class ApplyLoanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_apply_a_loan()
    {
        TestTime::freeze();
        $user = \factory(User::class)->create();

        $termEndedAt = Carbon::now()->addDays(30);
        $total = Money::SGD(450000);

        $loan = \app(ApplyLoan::class)(
            $user,
            'I need a loan to repay my debt',
            $total,
            $termEndedAt
        );

        $this->assertInstanceOf(Loan::class, $loan);
        $this->assertSame($user, $loan->getRelation('customer'));
        $this->assertSame(450000, $loan->amount);
        $this->assertSame('SGD', $loan->currency);
        $this->assertSame(Carbon::now()->toDatetimeString(), $loan->term_started_at->toDatetimeString());
        $this->assertSame($termEndedAt->toDatetimeString(), $loan->term_ended_at->toDatetimeString());
        $this->assertSame('I need a loan to repay my debt', $loan->description);
    }

    /** @test */
    public function user_can_apply_a_loan_that_starts_tomorrow()
    {
        TestTime::freeze();
        $user = \factory(User::class)->create();

        $termStartedAt = Carbon::now()->addDay(1);
        $termEndedAt = Carbon::now()->addDays(30);
        $total = Money::SGD(450000);

        $loan = \app(ApplyLoan::class)(
            $user,
            'I need a loan to repay my debt',
            $total,
            $termEndedAt,
            $termStartedAt
        );

        $this->assertInstanceOf(Loan::class, $loan);
        $this->assertSame($user, $loan->getRelation('customer'));
        $this->assertSame(450000, $loan->amount);
        $this->assertSame('SGD', $loan->currency);
        $this->assertSame($termStartedAt->toDatetimeString(), $loan->term_started_at->toDatetimeString());
        $this->assertSame($termEndedAt->toDatetimeString(), $loan->term_ended_at->toDatetimeString());
        $this->assertSame('I need a loan to repay my debt', $loan->description);
    }

    /** @test */
    public function user_cant_apply_a_loan_that_starts_after_term_ended()
    {
        $this->expectException('Illuminate\Validation\ValidationException');

        TestTime::freeze();
        $user = \factory(User::class)->create();

        $termStartedAt = Carbon::now()->addDay(40);
        $termEndedAt = Carbon::now()->addDays(30);
        $total = Money::SGD(450000);

        $loan = \app(ApplyLoan::class)(
            $user,
            'I need a loan to repay my debt',
            $total,
            $termEndedAt,
            $termStartedAt
        );
    }

    /** @test */
    public function user_can_apply_more_than_one_loan()
    {
        $user = \factory(User::class)->create();
        $firstLoan = \factory(Loan::class)->create([
            'user_id' => $user->getKey(),
        ]);

        $termEndedAt = Carbon::now()->addDays(30);
        $total = Money::SGD(450000);

        $secondLoan = \app(ApplyLoan::class)(
            $user,
            'I need a loan to repay my debt',
            $total,
            $termEndedAt
        );

        $this->assertSame(2, $user->loans->count());

        $this->assertTrue($user->loans[0]->is($firstLoan));
        $this->assertTrue($user->loans[1]->is($secondLoan));
    }
}

<?php

namespace Tests\Feature\Actions;

use App\Actions\ApplyLoan;
use App\Loan;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Money\Money;
use Spatie\TestTime\TestTime;
use Tests\JsonInspector;
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

        $loan = (new ApplyLoan())(
            $user,
            'I need a loan to repay my debt',
            $total,
            $termEndedAt
        );

        $this->assertInstanceOf(Loan::class, $loan);
        $this->assertSame($user, $loan->getRelation('customer'));
        $this->assertSame(450000, $loan->amount);
        $this->assertSame('SGD', $loan->currency);
        $this->assertSame(Carbon::today()->toDatetimeString(), $loan->term_started_at->toDatetimeString());
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

        $loan = (new ApplyLoan())(
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

    /**
     * @test
     * @dataProvider negativeOrZeroAmount
     */
    public function user_cant_apply_a_loan_with_negative_or_zero_amount($amount)
    {
        $this->withoutExceptionHandling();

        TestTime::freeze();
        $user = \factory(User::class)->create();

        $termEndedAt = Carbon::now()->addDays(30);
        $total = Money::SGD($amount);

        $this->expectValidationException(function () use ($user, $total, $termEndedAt) {
            $loan = (new ApplyLoan())(
                $user,
                'I need a loan to repay my debt',
                $total,
                $termEndedAt
            );
        }, [
            'total' => ['Loan amount should be higher than 0'],
        ]);
    }

    /** @test */
    public function user_cant_apply_a_loan_that_starts_after_term_ended()
    {
        $this->withoutExceptionHandling();

        TestTime::freeze();
        $user = \factory(User::class)->create();

        $termStartedAt = Carbon::now()->addDay(40);
        $termEndedAt = Carbon::now()->addDays(30);
        $total = Money::SGD(450000);

        try {
            $loan = (new ApplyLoan())(
                $user,
                'I need a loan to repay my debt',
                $total,
                $termEndedAt,
                $termStartedAt
            );

            $this->fail('The test passes validation when it should have failed.');
        } catch (ValidationException $e) {
            $this->assertEquals(
                'Term start date should be less than term end date',
                $e->validator->errors()->first('termStartedAt')
            );
        }
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

        $secondLoan = (new ApplyLoan())(
            $user,
            'I need a loan to repay my debt',
            $total,
            $termEndedAt
        );

        $this->assertSame(2, $user->loans->count());

        $this->assertTrue($user->loans[0]->is($firstLoan));
        $this->assertTrue($user->loans[1]->is($secondLoan));
    }

    public function negativeOrZeroAmount()
    {
        yield '$0' => [0];
        yield '$-1.00' => [-100];
        yield '$-10.00' => [-1000];
        yield '$-1000.00' => [-100000];
    }
}

<?php

namespace Tests\Feature\Actions;

use App\Actions\MakePayment;
use App\Loan;
use App\Repayment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Money\Money;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class MakePaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_make_repayment_to_a_loan()
    {
        TestTime::freeze('Y-m-d', '2019-08-01');

        $loan = \factory(Loan::class)->create([
            'amount' => 100000,
            'currency' => 'SGD',
        ]);

        TestTime::subDays(7);

        $payment = Money::SGD(20000);

        $repayment = (new MakePayment())($loan, $payment, 'Test user can make repayment');

        $this->assertInstanceOf(Repayment::class, $repayment);
        $this->assertSame(20000, $repayment->amount);
        $this->assertSame('SGD', $repayment->currency);
        $this->assertSame(Carbon::now()->toDatetimeString(), $repayment->occured_at->toDatetimeString());

        $this->assertSame('80000', $loan->outstanding()->getAmount());
    }

    /** @test */
    public function user_can_make_full_settlement_to_a_loan()
    {
        TestTime::freeze('Y-m-d', '2019-08-01');

        $loan = \factory(Loan::class)->create([
            'amount' => 100000,
            'currency' => 'SGD',
        ]);

        TestTime::subDays(7);

        $payment = Money::SGD(100000);

        $repayment = (new MakePayment())($loan, $payment, 'Test user can make repayment');

        $this->assertInstanceOf(Repayment::class, $repayment);
        $this->assertSame(100000, $repayment->amount);
        $this->assertSame('SGD', $repayment->currency);
        $this->assertSame(Carbon::now()->toDatetimeString(), $repayment->occured_at->toDatetimeString());

        $this->assertSame('0', $loan->outstanding()->getAmount());
    }

    /** @test */
    public function user_can_make_repayment_to_a_loan_on_specific_time()
    {
        TestTime::freeze('Y-m-d', '2019-08-01');

        $loan = \factory(Loan::class)->create([
            'amount' => 100000,
            'currency' => 'SGD',
        ]);

        $occuredAt = Carbon::now()->addDays(4);

        $payment = Money::SGD(20000);

        $repayment = (new MakePayment())($loan, $payment, 'Test user can make repayment', $occuredAt);

        $this->assertInstanceOf(Repayment::class, $repayment);
        $this->assertSame(20000, $repayment->amount);
        $this->assertSame('SGD', $repayment->currency);
        $this->assertSame($occuredAt->toDatetimeString(), $repayment->occured_at->toDatetimeString());

        $this->assertSame('80000', $loan->outstanding()->getAmount());
    }

    /** @test */
    public function user_cant_make_repayment_to_a_loan_on_different_currency()
    {
        TestTime::freeze('Y-m-d', '2019-08-01');

        $loan = \factory(Loan::class)->create([
            'amount' => 100000,
            'currency' => 'SGD',
        ]);

        $occuredAt = Carbon::now()->addDays(4);

        $payment = Money::USD(20000);

        try {
            $repayment = (new MakePayment())($loan, $payment, 'Test user can make repayment', $occuredAt);

            $this->fail('The test passes validation when it should have failed.');
        } catch (ValidationException $e) {
            $this->assertEquals(
                'Unable to accept payment currency different from loan currency',
                $e->validator->errors()->first('total')
            );
        }
    }


    /** @test */
    public function user_cant_make_repayment_to_a_loan_higher_than_outstanding_amount()
    {
        TestTime::freeze('Y-m-d', '2019-08-01');

        $loan = \factory(Loan::class)->create([
            'amount' => 100000,
            'currency' => 'SGD',
        ]);

        $occuredAt = Carbon::now()->addDays(4);

        $payment = Money::SGD(120000);

        try {
            $repayment = (new MakePayment())($loan, $payment, 'Test user can make repayment', $occuredAt);

            $this->fail('The test passes validation when it should have failed.');
        } catch (ValidationException $e) {
            $this->assertEquals(
                'Unable to accept payment amount higher than outstanding amount',
                $e->validator->errors()->first('total')
            );
        }
    }
}

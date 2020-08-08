<?php

namespace Tests\Feature;

use App\Loan;
use App\Repayment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Money\Money;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_properly_set_dues()
    {
        TestTime::freeze('Y-m-d', '2020-08-01');

        $loan = \factory(Loan::class)->create([
            'amount' => 400000,
            'term_started_at' => Carbon::today(),
            'term_ended_at' => Carbon::createFromFormat('Y-m-d', '2020-08-20'),
        ]);

        $this->assertSame(133334, $loan->due_amount);
        $this->assertSame('2020-08-06', $loan->due_at->toDateString());
    }

    /** @test */
    public function it_can_calculate_current_loan_installment()
    {
        $loan = \factory(Loan::class)->create();

        \factory(Repayment::class, 4)->create([
            'loan_id' => $loan->id,
            'currency' => $loan->currency,
            'amount' => 2000,
        ]);

        $installment = $loan->installment();

        $this->assertInstanceOf(Money::class, $installment);
        $this->assertSame('8000', $installment->getAmount());
        $this->assertSame($loan->currency, (string) $installment->getCurrency());
    }

    /** @test */
    public function it_can_calculate_current_loan_installment_when_no_repayment_has_been_made()
    {
        $loan = \factory(Loan::class)->create();

        $installment = $loan->installment();

        $this->assertInstanceOf(Money::class, $installment);
        $this->assertSame('0', $installment->getAmount());
        $this->assertSame($loan->currency, (string) $installment->getCurrency());
    }

    /** @test */
    public function it_can_calculate_current_loan_outstanding()
    {
        $loan = \factory(Loan::class)->create([
            'amount' => 120000,
        ]);

        \factory(Repayment::class, 4)->create([
            'loan_id' => $loan->id,
            'currency' => $loan->currency,
            'amount' => 2000,
        ]);

        $outstanding = $loan->outstanding();

        $this->assertInstanceOf(Money::class, $outstanding);
        $this->assertSame('112000', $outstanding->getAmount());
        $this->assertSame($loan->currency, (string) $outstanding->getCurrency());
    }

    /** @test */
    public function it_can_calculate_current_loan_outstanding_when_no_repayment_has_been_made()
    {
        $loan = \factory(Loan::class)->create([
            'amount' => 120000,
        ]);

        $outstanding = $loan->outstanding();

        $this->assertInstanceOf(Money::class, $outstanding);
        $this->assertSame('120000', $outstanding->getAmount());
        $this->assertSame($loan->currency, (string) $outstanding->getCurrency());
    }
}

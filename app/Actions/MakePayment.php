<?php

namespace App\Actions;

use App\Loan;
use App\Repayment;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;
use Money\Money;

class MakePayment
{
    /**
     * Make payment to a loan.
     */
    public function __invoke(Loan $loan, Money $total, string $description, ?CarbonInterface $occuredAt = null)
    {
        $this->validatePayment($loan, $total);

        if (\is_null($occuredAt)) {
            $occuredAt = Carbon::now();
        }

        $repayment = (new Repayment())->forceFill([
            'loan_id' => $loan->getKey(),
            'description' => $description,
            'total' => $total,
            'occured_at' => $occuredAt,
        ]);

        $repayment->saveOrFail();

        return $repayment->setRelation('loan', $loan);
    }

    /**
     * Validate payment amount.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatePayment(Loan $loan, Money $total): void
    {
        if (((string) $total->getCurrency()) !== $loan->currency) {
            throw ValidationException::withMessages(['total' => ['Unable to accept payment currency different from loan currency']]);
        }

        if ($total->greaterThan($loan->outstanding())) {
            throw ValidationException::withMessages(['total' => ['Unable to accept payment amount higher than outstanding amount']]);
        }
    }
}

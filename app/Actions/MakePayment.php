<?php

namespace App\Actions;

use App\Loan;
use App\Repayment;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Money\Money;

class MakePayment
{
    /**
     * Make payment to a loan.
     */
    public function __invoke(Loan $loan, string $description, Money $total, ?CarbonInterface $occuredAt = null)
    {
        $fullSettlement = false;
        $outstanding = $loan->outstanding();

        $this->validatePayment($loan, $total, $outstanding);

        $fullSettlement = $total->equals($outstanding);

        if (\is_null($occuredAt)) {
            $occuredAt = Carbon::now();
        }

        $repayment = (new Repayment())->forceFill([
            'loan_id' => $loan->getKey(),
            'description' => $description,
            'total' => $total,
            'occured_at' => $occuredAt,
        ]);

        DB::transaction(function () use ($loan, $repayment, $fullSettlement) {
            $repayment->save();

            if ($fullSettlement === true) {
                $loan->markFullSettlementFrom($repayment);
            } else {
                $loan->syncDues();
            }

            $loan->save();
        });

        return $repayment->setRelation('loan', $loan);
    }

    /**
     * Validate payment amount.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validatePayment(Loan $loan, Money $total, Money $outstanding): void
    {
        if ($outstanding->isZero()) {
            throw ValidationException::withMessages(['outstanding' => ['Unable to accept payment for full settlement loan']]);
        }

        if (((string) $total->getCurrency()) !== $loan->currency) {
            throw ValidationException::withMessages(['total' => ['Unable to accept payment currency different from loan currency']]);
        }

        if ($total->greaterThan($outstanding)) {
            throw ValidationException::withMessages(['total' => ['Unable to accept payment amount higher than outstanding amount']]);
        }
    }
}

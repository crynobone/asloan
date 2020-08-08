<?php

namespace App\Actions;

use App\Loan;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;
use Money\Money;

class ApplyLoan
{
    /**
     * Apply a loan.
     */
    public function __invoke(
        User $user,
        ?string $description,
        Money $total,
        CarbonInterface $termEndedAt,
        ?CarbonInterface $termStartedAt = null,
        ?int $paymentInterval = null
    ): Loan {
        if (\is_null($termStartedAt)) {
            $termStartedAt = Carbon::now();
        }

        $this->validateTermPeriod($termStartedAt, $termEndedAt);
        $this->validateAmount($total);

        $loan = (new Loan())->forceFill(\array_filter([
            'user_id' => $user->getKey(),
            'payment_interval' => $paymentInterval,
            'description' => $description,
            'total' => $total,
            'term_started_at' => $termStartedAt,
            'term_ended_at' => $termEndedAt,
        ]));

        $loan->saveOrFail();

        return $loan->setRelation('customer', $user);
    }

    /**
     * Validate loan amount.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateAmount(Money $total): void
    {
        if ($total->isNegative() || $total->isZero()) {
            throw ValidationException::withMessages(['total' => ['Loan amount should be higher than 0']]);
        }
    }

    /**
     * Validate term period.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateTermPeriod(CarbonInterface $termStartedAt, CarbonInterface $termEndedAt): void
    {
        if ($termEndedAt->lessThanOrEqualTo($termStartedAt)) {
            throw ValidationException::withMessages(['termStartedAt' => ['Term start date should be less than term end date']]);
        }
    }
}

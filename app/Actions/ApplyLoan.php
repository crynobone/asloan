<?php

namespace App\Actions;

use App\Loan;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;
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
}

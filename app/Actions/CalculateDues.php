<?php

namespace App\Actions;

use App\Loan;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class CalculateDues
{
    /**
     * Minimum days before customer need to make first repayment.
     *
     * @var int
     */
    public const MINIMUM_DAYS_BEFORE_DUE = 4;

    /**
     * Calculate dues on a loan.
     */
    public function __invoke(Loan $loan)
    {
        $weeks = $loan->term_started_at->diffInWeeks($loan->term_ended_at);
        $dueDay = $loan->term_ended_at->dayOfWeek;

        $nextDueDate = $loan->due_at instanceof CarbonInterface
            ? $loan->due_at->copy()->next($dueDay)
            : $loan->term_started_at->copy()->next($dueDay);

        if ($loan->term_started_at->diffInDays($nextDueDate) < self::MINIMUM_DAYS_BEFORE_DUE) {
            $nextDueDate->addWeek(1);
        }

        if ($nextDueDate->greaterThan($loan->term_ended_at)) {
            $nextDueDate = $loan->term_ended_at->copy();
        }

        $outstanding = $loan->outstanding();
        $outstandingWeeks = $nextDueDate->diffInWeeks($loan->term_ended_at) + 1;

        if ($outstandingWeeks > 1) {
            [$nextDueAmount] = $outstanding->allocateTo($outstandingWeeks);
        } else {
            $nextDueAmount = $outstanding;
        }

        return [
            'weeks' => $weeks,
            'dueDay' => Carbon::getDays()[$dueDay],
            'nextDueAmount' => $nextDueAmount,
            'nextDueDate' => $nextDueDate,
        ];
    }
}

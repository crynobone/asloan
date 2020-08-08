<?php

namespace App\Observers;

use App\Actions\CalculateDues;
use App\Loan;
use Carbon\Carbon;

class LoanObserver
{
    /**
     * Handle the loan "creating" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function creating(Loan $loan)
    {
        if (\is_null($loan->term_started_at)) {
            $loan->term_started_at = Carbon::now();
        }

        $dues = \app(CalculateDues::class)($loan);

        $loan->due_total = $dues['nextDueAmount'];
        $loan->due_at = $dues['nextDueDate'];
    }

    /**
     * Handle the loan "updated" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function updated(Loan $loan)
    {
        //
    }

    /**
     * Handle the loan "deleted" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function deleted(Loan $loan)
    {
        //
    }

    /**
     * Handle the loan "restored" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function restored(Loan $loan)
    {
        //
    }

    /**
     * Handle the loan "force deleted" event.
     *
     * @param  \App\Loan  $loan
     * @return void
     */
    public function forceDeleted(Loan $loan)
    {
        //
    }
}

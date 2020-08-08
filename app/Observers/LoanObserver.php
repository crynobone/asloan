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
     * @return void
     */
    public function creating(Loan $loan)
    {
        if (\is_null($loan->term_started_at)) {
            $loan->term_started_at = Carbon::now();
        }

        $loan->syncDues();
    }

    /**
     * Handle the loan "updated" event.
     *
     * @return void
     */
    public function updated(Loan $loan)
    {
        //
    }

    /**
     * Handle the loan "deleted" event.
     *
     * @return void
     */
    public function deleted(Loan $loan)
    {
        //
    }

    /**
     * Handle the loan "restored" event.
     *
     * @return void
     */
    public function restored(Loan $loan)
    {
        //
    }

    /**
     * Handle the loan "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Loan $loan)
    {
        //
    }
}

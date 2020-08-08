<?php

namespace App;

use App\Actions\CalculateDues;
use App\Actions\MakePayment;
use App\Observers\LoanObserver;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;
use Money\Money;

class Loan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loans';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'repayment_interval' => 'int',
        'total' => Casts\Money::class.':amount,currency',
        'due_total' => Casts\Money::class.':due_amount,currency',
        'due_at' => 'datetime',
        'term_started_at' => 'datetime',
        'term_ended_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted()
    {
        static::observe(new LoanObserver());
    }

    /**
     * Loan belongs to a User (customer).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Loan has many repayments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function repayments()
    {
        return $this->hasMany(Repayment::class, 'loan_id', 'id');
    }

    /**
     * Check if loan has been competed.
     */
    public function isCompleted(): bool
    {
        return ! \is_null($this->completed_at);
    }

    /**
     * Get loan total.
     *
     * @return \Money\Money
     */
    public function total()
    {
        return $this->total;
    }

    /**
     * Get loan outstanding.
     *
     * @return \Money\Money
     */
    public function outstanding()
    {
        return $this->total->subtract($this->installment());
    }

    /**
     * Get loan installment.
     *
     * @return \Money\Money
     */
    public function installment()
    {
        $amount = Repayment::where('loan_id', '=', $this->getKey())
            ->toBase()
            ->sum('amount');

        return new Money($amount, new Currency($this->currency));
    }

    /**
     * Make payment.
     */
    public function syncDues(): void
    {
        $dues = \app(CalculateDues::class)($this, $this->due_at);

        $this->due_total = $dues['nextDueAmount'];
        $this->due_at = $dues['nextDueDate'];
    }

    /**
     * Make payment.
     */
    public function makePayment(
        string $description,
        Money $total,
        ?CarbonInterface $occuredAt = null
    ): Repayment {
        $makePayment = \app(MakePayment::class);

        return $makePayment($this, $description, $total, $occuredAt);
    }

    /**
     * Mark full settlement.
     */
    public function markFullSettlementFrom(Repayment $repayment): void
    {
        $this->due_total = new Money('0', new Currency($this->currency));
        $this->due_at = null;
        $this->completed_at = $repayment->occured_at->copy();
    }
}

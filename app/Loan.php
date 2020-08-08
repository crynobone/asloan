<?php

namespace App;

use App\Observers\LoanObserver;
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
}

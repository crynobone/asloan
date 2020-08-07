<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        'price' => Casts\Money::class.':amount,currency',
        'due_price' => Casts\Money::class.':due_amount,currency',
        'due_at' => 'datetime',
        'term_started_at' => 'datetime',
        'term_ended_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
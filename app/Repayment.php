<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'repayments';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total' => Casts\Money::class.':amount,currency',
        'occured_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Repayment belongs to loan.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'id');
    }
}

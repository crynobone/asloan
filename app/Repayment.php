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
        'occured_at' => 'datetime',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}

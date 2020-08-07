<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Loan;
use App\Repayment;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Repayment::class, function (Faker $faker) {
    return [
        'loan_id' => factory(Loan::class),
        'currency' => 'SGD',
        'amount' => 4000,
        'occured_at' => Carbon::now(),
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Loan;
use App\Repayment;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Repayment::class, function (Faker $faker) {
    $now = Carbon::now();

    return [
        'loan_id' => factory(Loan::class),
        'description' => 'Test repayment on '.$now->toDateString(),
        'currency' => 'SGD',
        'amount' => 4000,
        'occured_at' => $now,
    ];
});

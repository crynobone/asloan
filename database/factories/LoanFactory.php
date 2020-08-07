<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Loan;
use App\User;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Loan::class, function (Faker $faker) {
    $termStarted = Carbon::today();
    $termEnded = $termStarted->copy()->addDays(7);

    return [
        'user_id' => \factory(User::class),
        'description' => $faker->realText(20),
        'repayment_interval' => 7,
        'currency' => 'SGD',
        'amount' => 400000,
        'term_started_at' => $termStarted,
        'term_ended_at' => $termEnded,
    ];
});

$factory->state(Loan::class, 'two-weeks', function ($faker) {
    $termStarted = Carbon::today();
    $termEnded = $termStarted->copy()->addDays(14);

    return [
        'term_started_at' => $termStarted,
        'term_ended_at' => $termEnded,
    ];
});


$factory->state(Loan::class, 'four-weeks', function ($faker) {
    $termStarted = Carbon::today();
    $termEnded = $termStarted->copy()->addDays(28);

    return [
        'term_started_at' => $termStarted,
        'term_ended_at' => $termEnded,
    ];
});

$factory->state(Loan::class, '10-days', function ($faker) {
    $termStarted = Carbon::today();
    $termEnded = $termStarted->copy()->addDays(10);

    return [
        'term_started_at' => $termStarted,
        'term_ended_at' => $termEnded,
    ];
});

$factory->state(Loan::class, '20-days', function ($faker) {
    $termStarted = Carbon::today();
    $termEnded = $termStarted->copy()->addDays(20);

    return [
        'term_started_at' => $termStarted,
        'term_ended_at' => $termEnded,
    ];
});

$factory->state(Loan::class, '30-days', function ($faker) {
    $termStarted = Carbon::today();
    $termEnded = $termStarted->copy()->addDays(30);

    return [
        'term_started_at' => $termStarted,
        'term_ended_at' => $termEnded,
    ];
});

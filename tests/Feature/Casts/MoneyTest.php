<?php

namespace Tests\Feature\Casts;

use App\Repayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Money\Money;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_cast_to_money()
    {
        $repayment = \factory(Repayment::class)->make([
            'amount' => 3500,
            'currency' => 'SGD',
        ]);

        $money = $repayment->price;

        $this->assertInstanceOf(Money::class, $money);
        $this->assertSame(3500, (int) $money->getAmount());
        $this->assertSame('SGD', (string) $money->getCurrency());
    }

    /** @test */
    public function it_cast_from_money()
    {
        $repayment = \factory(Repayment::class)->make([
            'amount' => 3500,
            'currency' => 'SGD',
        ]);

        $repayment->price = Money::USD(4000);

        $this->assertSame(4000, $repayment->amount);
        $this->assertSame('USD', $repayment->currency);
    }
}

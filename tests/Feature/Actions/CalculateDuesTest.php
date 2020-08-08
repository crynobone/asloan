<?php

namespace Tests\Feature\Actions;

use App\Actions\CalculateDues;
use App\Loan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Spatie\TestTime\TestTime;
use Tests\TestCase;

class CalculateDuesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider expectedDuesDataProvider
     */
    public function it_can_calculate_dues($endDate, $weeks, $nextDueAmount, $nextDueDate)
    {
        TestTime::freeze('Y-m-d', '2020-08-01');

        $loan = Event::fakeFor(function () use ($endDate) {
            return \factory(Loan::class)->create([
                'amount' => 400000,
                'term_started_at' => Carbon::today(),
                'term_ended_at' => Carbon::createFromFormat('Y-m-d', $endDate),
            ]);
        });

        $dues = (new CalculateDues())($loan, null);

        $this->assertSame($weeks, $dues['weeks']);
        $this->assertSame($nextDueAmount, $dues['nextDueAmount']->getAmount());
        $this->assertSame($nextDueDate, $dues['nextDueDate']->toDateString());
    }

    public function expectedDuesDataProvider()
    {
        // 1 week or less
        yield '1 day' => ['2020-08-02', 0, '400000', '2020-08-02'];
        yield '2 days' => ['2020-08-03', 0, '400000', '2020-08-03'];
        yield '3 days' => ['2020-08-04', 0, '400000', '2020-08-04'];
        yield '4 days' => ['2020-08-05', 0, '400000', '2020-08-05'];
        yield '5 days' => ['2020-08-06', 0, '400000', '2020-08-06'];
        yield '6 days' => ['2020-08-07', 0, '400000', '2020-08-07'];
        yield '7 days' => ['2020-08-08', 1, '400000', '2020-08-08'];

        // 2 weeks or less
        yield '12 days' => ['2020-08-13', 1, '200000', '2020-08-06'];
        yield '13 days' => ['2020-08-14', 1, '200000', '2020-08-07'];
        yield '14 days' => ['2020-08-15', 2, '200000', '2020-08-08'];

        // 3 weeks or less
        yield '19 days' => ['2020-08-20', 2, '133334', '2020-08-06'];
        yield '20 days' => ['2020-08-21', 2, '133334', '2020-08-07'];
        yield '21 days' => ['2020-08-22', 3, '133334', '2020-08-08'];

        // 4 weeks or less
        yield '26 days' => ['2020-08-27', 3, '100000', '2020-08-06'];
        yield '27 days' => ['2020-08-28', 3, '100000', '2020-08-07'];
        yield '28 days' => ['2020-08-29', 4, '100000', '2020-08-08'];

        // extras
        yield '30 days' => ['2020-08-30', 4, '100000', '2020-08-09'];
        yield '31 days' => ['2020-08-31', 4, '100000', '2020-08-10'];
        yield '32 days' => ['2020-09-01', 4, '100000', '2020-08-11'];
        yield '33 days' => ['2020-09-02', 4, '80000', '2020-08-05'];
        yield '34 days' => ['2020-09-03', 4, '80000', '2020-08-06'];
        yield '35 days' => ['2020-09-04', 4, '80000', '2020-08-07'];
        yield '36 days' => ['2020-09-05', 5, '80000', '2020-08-08'];
        yield '37 days' => ['2020-09-06', 5, '80000', '2020-08-09'];
        yield '38 days' => ['2020-09-07', 5, '80000', '2020-08-10'];
        yield '39 days' => ['2020-09-08', 5, '80000', '2020-08-11'];

        yield '40 days' => ['2020-09-09', 5, '66667', '2020-08-05'];
    }
}

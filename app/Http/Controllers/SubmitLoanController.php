<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanApplicationRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function App\as_money;
use function App\present_money;

class SubmitLoanController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LoanApplicationRequest $request)
    {
        $data = $request->validated();

        $total = as_money($data['total'], $data['currency']);

        $loan = $request->user()->applyLoan(
            $data['description'],
            $total,
            Carbon::parse($data['term_ended_at'])
        );

        return redirect()->route('home')->with('status', 'Successfully apply loan '.present_money($total));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanApplicationRequest;
use Illuminate\Http\Request;
use function App\as_money;

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

        $loan = $request->user()->applyLoan(
            $data['description'],
            as_money($data['total'], $data['currency']),
            today()->addWeeks(4)
        );

        return redirect()->route('home');
    }
}

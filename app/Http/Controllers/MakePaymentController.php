<?php

namespace App\Http\Controllers;

use App\Http\Requests\MakePaymentRequest;
use App\Loan;
use Illuminate\Http\Request;
use function App\as_money;
use function App\present_money;

class MakePaymentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(MakePaymentRequest $request, Loan $loan)
    {
        $data = $request->validated();

        if ($data['currency'] !== $loan->currency) {
            return \redirect()
                ->back()
                ->with('error', "Unable to make payment with other currency than {$loan->currency}");
        }

        $total = as_money($data['total'], $data['currency']);

        $loan->makePayment(
            'Online payment from '.$request->ip(), $total
        );

        return redirect()->route('home')->with('status', 'Successfully make loan payment '.present_money($total));
    }
}

<?php

namespace App\Http\Controllers;

use App\Loan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $loans = Loan::where('user_id', '=', $request->user()->id)->paginate();

        return \view('home', \compact('loans'));
    }
}

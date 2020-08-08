@extends('layouts.app')

@section('content')
    <div class="flex items-center">
        <div class="container mx-auto">

            <x-flash-message />

            <div class="flex flex-col break-words bg-white border border-2 rounded shadow-md">

                <div class="flex justify-between bg-gray-100 p-6 mb-0">
                    <h2 class="flex-1 font-semibold text-2xl text-gray-700">Loans</h2>

                    <span class="inline-flex rounded-md shadow-sm">
                      <a href="{{ route('apply-loan') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150">
                        Apply Loan
                      </a>
                    </span>
                </div>

                <x-loans-table :loans="$loans"/>
            </div>
        </div>
    </div>
@endsection

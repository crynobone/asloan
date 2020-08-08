@extends('layouts.app')

@section('content')
  <div class="flex items-center">
    <div class="container mx-auto">

      <x-flash-message />

      <div class="bg-white border border-2 rounded shadow-md p-6">
        <form method="POST" action="{{ route('submit-loan') }}">
          @csrf
          <div>
            <h3 class="text-2xl leading-6 font-medium text-gray-900">
              Apply Loan
            </h3>
            <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
              This information will be displayed publicly so be careful what you share.
            </p>
          </div>
          <div class="mt-6 sm:mt-5">
            <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
              <label for="description" class="block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2">
                Description
              </label>
              <div class="mt-1 sm:mt-0 sm:col-span-2">
                <div class="max-w-lg relative flex rounded-md shadow-sm">
                  <textarea id="description" name="description" rows="3" class="form-textarea block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5 @error('description') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red @enderror">{{ old('description') }}</textarea>
                  @error('description')
                  <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  @enderror
                </div>
                @error('description')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @else
                <p class="mt-2 text-sm text-gray-500">Description about the loan.</p>
                @enderror
              </div>
            </div>

            <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center sm:border-t sm:border-gray-200 sm:pt-5">
              <label for="total" class="block text-sm leading-5 font-medium text-gray-700">
                Loan Amount
              </label>
              <div class="mt-2 sm:mt-0 sm:col-span-2">
                <div class="mt-1 relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm sm:leading-5">
                      $
                    </span>
                  </div>
                  <input id="total" name="total" type="number" class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5 @error('total') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red @enderror" placeholder="0.00" aria-describedby="total-currency" value="{{ old('total') }}">
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm sm:leading-5" id="total-currency">
                      {{ $currency }}
                    </span>
                  </div>
                  <input type="hidden" name="currency" value="{{ $currency }}">
                </div>

                @error('total')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="mt-6 sm:mt-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:items-center sm:border-t sm:border-gray-200 sm:pt-5">
              <label for="term_ended_at" class="block text-sm leading-5 font-medium text-gray-700">
                Loan Amount
              </label>
              <div class="mt-2 sm:mt-0 sm:col-span-2">
                <div class="mt-1 relative rounded-md shadow-sm">
                  <input id="term_ended_at" name="term_ended_at" type="date" class="form-input block w-full sm:text-sm sm:leading-5 @error('term_ended_at') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red @enderror" placeholder="2 weeks" value="{{ old('term_ended_at') }}">
                </div>

                @error('term_ended_at')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>


          </div>
          <div class="mt-8 border-t border-gray-200 pt-5">
            <div class="flex justify-end">
              <span class="inline-flex rounded-md shadow-sm">
                <a href="{{ route('home') }}" class="py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out">
                  Cancel
                </a>
              </span>
              <span class="ml-3 inline-flex rounded-md shadow-sm">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                  Save
                </button>
              </span>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

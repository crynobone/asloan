<div class="flex flex-col">
  <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
    <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
              Description
            </th>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
              Loan Amount
            </th>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
              Outstanding Amount
            </th>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
              Loan Due Amount
            </th>
            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
              Loan Due At
            </th>
            <th class="px-6 py-3 bg-gray-50"></th>
          </tr>
        </thead>
        <tbody>
          @if ($loans->isNotEmpty())
          @foreach ($loans as $loop => $loan)
          <tr class="bg-white"> <!-- odd should show 'bg-gray-50' -->
            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
              {{ $loan->description }}
            </td>
            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
              {{ App\present_money($loan->total) }}
            </td>
            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
              {{ App\present_money($loan->outstanding()) }}
            </td>
            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
              {{ App\present_money($loan->due_total) }}
            </td>
            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">
              @if (is_null($loan->completed_at))
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-yellow-100 text-yellow-800">
                {{ $loan->due_at->format('jS \\of F Y') }}
              </span>
              @else
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 bg-teal-100 text-teal-800">
                Completed on {{ $loan->completed_at->format('jS \\of F Y') }}
              </span>
              @endif
            </td>
            <td class="px-6 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
              @if (is_null($loan->completed_at))
              <form method="POST" action="{{ route('make-payment', [$loan]) }}">
                @csrf
                <div class="flex justify-between items-center">
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm sm:leading-5">
                        $
                      </span>
                    </div>
                    <input id="total" type="number" step="0.01" name="total" class="form-input block w-full pl-7 pr-12 sm:text-sm sm:leading-5 @error('total') border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red @enderror" placeholder="0.00" aria-describedby="total-currency" value="{{ App\present_money($loan->due_total, false) }}">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm sm:leading-5" id="total-currency">
                        {{ $loan->currency }}
                      </span>
                      <input type="hidden" name="currency" value="{{ $loan->currency }}">
                    </div>
                  </div>
                  <div class="ml-2">
                    <span class="inline-flex rounded-md shadow-sm">
                      <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-50 focus:outline-none focus:border-indigo-300 focus:shadow-outline-indigo active:bg-indigo-200 transition ease-in-out duration-150">Pay</button>
                    </span>
                  </div>
                </div>

                @error('total')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </form>
              @endif
            </td>
          </tr>
          @endforeach
          @else
          <tr class="bg-white">
            <td colspan="6" class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900">
              <span class="inline-flex">You have no loan at the moment, Want to </span>
              <span class="inline-flex rounded-md shadow-sm">
                <a href="{{ route('apply-loan') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-50 focus:outline-none focus:border-indigo-300 focus:shadow-outline-indigo active:bg-indigo-200 transition ease-in-out duration-150">
                  Apply Loan
                </a>
              </span>
              <span class="inline-flex">now?</span>
            </td>
          </tr>
          @endif
        </tbody>
      </table>
      <x-pagination :paginator="$loans" />
    </div>
  </div>
</div>

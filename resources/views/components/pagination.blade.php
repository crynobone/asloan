@unless($paginator->onFirstPage() && ! $paginator->hasMorePages())
<nav class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
  <div class="hidden sm:block">
    <p class="text-sm leading-5 text-gray-700">
      Showing
      <span class="font-medium">{{ $paginator->firstItem() }}</span>
      to
      <span class="font-medium">{{ $paginator->lastItem() }}</span>
      of
      <span class="font-medium">{{ $paginator->total() }}</span>
      results
    </p>
  </div>
  <div class="flex-1 flex justify-between sm:justify-end">
    @unless($paginator->onFirstPage())
    <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
      Previous
    </a>
    @endunless
    @if($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
      Next
    </a>
    @endif
  </div>
</nav>
@endunless

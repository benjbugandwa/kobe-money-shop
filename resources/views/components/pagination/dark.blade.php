@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-400">
            Affichage de
            <span class="font-medium text-gray-200">{{ $paginator->firstItem() }}</span>
            a
            <span class="font-medium text-gray-200">{{ $paginator->lastItem() }}</span>
            sur
            <span class="font-medium text-gray-200">{{ $paginator->total() }}</span>
            resultats
        </p>

        <div class="inline-flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="inline-flex min-h-9 items-center rounded-md border border-gray-800 bg-gray-900 px-3 py-2 text-sm text-gray-600">
                    Precedent
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                    class="inline-flex min-h-9 items-center rounded-md border border-gray-700 bg-gray-900 px-3 py-2 text-sm text-gray-200 hover:bg-gray-800 disabled:opacity-60">
                    Precedent
                </button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-md border border-gray-800 bg-gray-900 px-3 py-2 text-sm text-gray-500">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-md border border-indigo-500 bg-indigo-600 px-3 py-2 text-sm font-semibold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                                class="inline-flex min-h-9 min-w-9 items-center justify-center rounded-md border border-gray-700 bg-gray-900 px-3 py-2 text-sm text-gray-200 hover:bg-gray-800 disabled:opacity-60">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                    class="inline-flex min-h-9 items-center rounded-md border border-gray-700 bg-gray-900 px-3 py-2 text-sm text-gray-200 hover:bg-gray-800 disabled:opacity-60">
                    Suivant
                </button>
            @else
                <span class="inline-flex min-h-9 items-center rounded-md border border-gray-800 bg-gray-900 px-3 py-2 text-sm text-gray-600">
                    Suivant
                </span>
            @endif
        </div>
    </nav>
@endif

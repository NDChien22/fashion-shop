@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Hiển thị
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    đến
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    từ
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    kết quả
                </p>
            </div>

            <div>
                <ul class="inline-flex items-center gap-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li>
                            <span
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-400 cursor-not-allowed">
                                <i class="ri-arrow-left-s-line"></i>
                            </span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                                class="inline-flex items-center justify-center rounded-lg border border-[#dcc8b8] bg-white px-3 py-2 text-sm font-semibold text-[#9d7a4a] hover:bg-[#faf8f5] transition">
                                <i class="ri-arrow-left-s-line"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li>
                                <span
                                    class="inline-flex items-center justify-center px-3 py-2 text-sm font-semibold text-gray-400">
                                    {{ $element }}
                                </span>
                            </li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li>
                                        <span
                                            class="inline-flex items-center justify-center rounded-lg bg-[#bc9c75] px-3 py-2 text-sm font-semibold text-white">
                                            {{ $page }}
                                        </span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $url }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-[#dcc8b8] bg-white px-3 py-2 text-sm font-semibold text-[#9d7a4a] hover:bg-[#faf8f5] transition">
                                            {{ $page }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li>
                            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                                class="inline-flex items-center justify-center rounded-lg border border-[#dcc8b8] bg-white px-3 py-2 text-sm font-semibold text-[#9d7a4a] hover:bg-[#faf8f5] transition">
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                        </li>
                    @else
                        <li>
                            <span
                                class="inline-flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-400 cursor-not-allowed">
                                <i class="ri-arrow-right-s-line"></i>
                            </span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>

        {{-- Mobile Pagination --}}
        <div class="flex flex-1 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-400 cursor-not-allowed">
                    <i class="ri-arrow-left-s-line"></i>
                    Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center gap-2 rounded-lg border border-[#dcc8b8] bg-white px-4 py-2 text-sm font-semibold text-[#9d7a4a] hover:bg-[#faf8f5] transition">
                    <i class="ri-arrow-left-s-line"></i>
                    Trước
                </a>
            @endif

            <span class="text-sm font-semibold text-gray-700">
                Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center gap-2 rounded-lg border border-[#dcc8b8] bg-white px-4 py-2 text-sm font-semibold text-[#9d7a4a] hover:bg-[#faf8f5] transition">
                    Sau
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            @else
                <span
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm font-semibold text-gray-400 cursor-not-allowed">
                    Sau
                    <i class="ri-arrow-right-s-line"></i>
                </span>
            @endif
        </div>
    </nav>
@endif

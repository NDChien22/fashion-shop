@props([
    'id' => 'productDetailModal',
    'title' => 'Chi tiet san pham',
    'maxWidth' => 'max-w-4xl',
    'show' => false,
    'closeAction' => null,
    'contentClass' => '',
])

<div id="{{ $id }}"
    class="{{ $show ? '' : 'hidden' }} fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    @if ($closeAction) wire:click.self="{{ $closeAction }}" @endif>
    <div class="bg-white w-full {{ $maxWidth }} rounded-3xl shadow-2xl overflow-hidden relative">
        <button
            @if ($closeAction) wire:click="{{ $closeAction }}"
            @else
                type="button" data-modal-close="{{ $id }}" @endif
            class="absolute top-5 right-5 w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 shadow-sm flex items-center justify-center z-10 transition-all">
            <i class="fa-solid fa-xmark text-gray-500"></i>
        </button>

        <div class="{{ $contentClass }}">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="px-6 py-4 border-t border-gray-100 bg-white">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>

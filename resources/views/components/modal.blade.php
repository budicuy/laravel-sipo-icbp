@props([
    'id' => null,
    'title' => null,
    'size' => 'md',
    'show' => false,
    'closeOnBackdrop' => true,
    'closeOnEscape' => true,
    'scrollable' => false
])

@php
    $modalId = $id ?? 'modal-' . uniqid();

    $sizeClasses = [
        'xs' => 'max-w-md',
        'sm' => 'max-w-lg',
        'md' => 'max-w-2xl',
        'lg' => 'max-w-4xl',
        'xl' => 'max-w-6xl',
        'full' => 'max-w-full mx-4'
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<!-- Modal Trigger Button (if provided) -->
@if(isset($trigger))
    {{ $trigger }}
@endif

<!-- Modal -->
<div
    id="{{ $modalId }}"
    class="fixed inset-0 z-50 overflow-y-auto {{ $show ? '' : 'hidden' }}"
    x-data="{
        open: {{ $show ? 'true' : 'false' }},
        init() {
            if (this.open) this.$el.classList.remove('hidden');
            @if($closeOnEscape)
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.open) {
                    this.close();
                }
            });
            @endif
        },
        show() {
            this.open = true;
            this.$el.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.open = false;
            this.$el.classList.add('hidden');
            document.body.style.overflow = 'auto';
            this.$dispatch('modal-closed', { id: '{{ $modalId }}' });
        }
    }"
    {{ $show ? '' : 'x-cloak' }}
>
    <!-- Backdrop -->
    <div
        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
        @if($closeOnBackdrop)
        @click="close()"
        @endif
    ></div>

    <!-- Modal Container -->
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <!-- Modal Panel -->
        <div
            class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:w-full {{ $sizeClass }}"
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.stop
        >
            <!-- Modal Header -->
            @if($title || isset($header))
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            @if(isset($icon))
                                <div class="flex-shrink-0 mr-3">
                                    {!! $icon !!}
                                </div>
                            @endif
                            <div>
                                @if($title)
                                    <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
                                @endif
                                @if(isset($subtitle))
                                    <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
                                @endif
                            </div>
                        </div>
                        <button
                            type="button"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500 transition-colors"
                            @click="close()"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @if(isset($header))
                        {{ $header }}
                    @endif
                </div>
            @endif

            <!-- Modal Body -->
            <div class="bg-white {{ $scrollable ? 'max-h-96 overflow-y-auto' : '' }}">
                @if(isset($body))
                    <div class="px-6 py-4">
                        {{ $body }}
                    </div>
                @else
                    <div class="px-6 py-4">
                        {{ $slot }}
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            @if(isset($footer) || isset($actions))
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    @if(isset($actions))
                        <div class="flex justify-end space-x-3">
                            {{ $actions }}
                        </div>
                    @endif
                    @if(isset($footer))
                        {{ $footer }}
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Trigger Script -->
@push('scripts')
<script>
// Global function to open modal
window.openModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal && modal._x_dataStack) {
        modal._x_dataStack[0].show();
    } else if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
};

// Global function to close modal
window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (modal && modal._x_dataStack) {
        modal._x_dataStack[0].close();
    } else if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
};
</script>
@endpush

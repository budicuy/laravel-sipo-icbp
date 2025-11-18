@extends('layouts.landing')

@section('page-title', $post->title . ' - SIPO')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('landing') }}"
                        class="inline-flex items-center text-gray-600 hover:text-purple-600 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
                <div class="text-sm text-gray-500">
                    {{ $post->created_at->format('d F Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Article Content -->
    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Article Header -->
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                {{ $post->title }}
            </h1>

            <div class="flex items-center text-sm text-gray-500 border-b border-gray-200 pb-6">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Dipublikasikan pada {{ $post->created_at->format('l, d F Y \p\u\k\u\l H:i') }} WITA
                @if($post->updated_at != $post->created_at)
                <span class="ml-4">
                    • Diperbarui pada {{ $post->updated_at->format('l, d F Y \p\u\k\u\l H:i') }} WITA
                </span>
                @endif
            </div>
        </header>

        <!-- Featured Image -->
        @if($post->image_path)
        <div class="mb-8">
            <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}"
                class="w-full h-64 md:h-96 object-cover rounded-lg shadow-lg">
        </div>
        @endif

        <!-- Article Body -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <div class="prose prose-lg max-w-none">
                {!! $post->body !!}
            </div>
        </div>

        <!-- Article Footer -->
        <footer class="mt-8 pt-8 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Artikel ini dipublikasikan oleh SIPO
                </div>
                <div class="flex space-x-4">
                    <!-- Share buttons can be added here if needed -->
                </div>
            </div>
        </footer>
    </article>

    <!-- Related Articles Section -->
    <section class="bg-white border-t border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Artikel Lainnya</h2>

            <div class="grid md:grid-cols-2 gap-6">
                @php
                $relatedPosts = \App\Models\Post::where('id', '!=', $post->id)
                ->latest()
                ->take(2)
                ->get();
                @endphp

                @forelse($relatedPosts as $relatedPost)
                <article
                    class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow border border-gray-100">
                    @if($relatedPost->image_path)
                    <div class="h-32 overflow-hidden">
                        <img src="{{ asset('storage/' . $relatedPost->image_path) }}" alt="{{ $relatedPost->title }}"
                            class="w-full h-full object-cover">
                    </div>
                    @endif

                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            <a href="{{ route('landing.posts.show', $relatedPost) }}"
                                class="hover:text-purple-600 transition-colors">
                                {{ $relatedPost->title }}
                            </a>
                        </h3>

                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                            {!! Str::limit(strip_tags($relatedPost->body), 100) !!}
                        </p>

                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500">
                                {{ $relatedPost->created_at->format('d M Y') }}
                            </span>
                            <a href="{{ route('landing.posts.show', $relatedPost) }}"
                                class="text-sm text-purple-600 hover:text-purple-700 font-medium">
                                Baca →
                            </a>
                        </div>
                    </div>
                </article>
                @empty
                <div class="md:col-span-2 text-center py-8">
                    <p class="text-gray-500">Belum ada artikel lainnya</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
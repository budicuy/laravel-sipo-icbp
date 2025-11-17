@extends('layouts.landing')

@section('page-title', 'Artikel & Berita - SIPO')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
                    {{ $posts->total() }} Artikel
                </div>
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">
                    Artikel & Berita
                </h1>
                <p class="text-xl md:text-2xl text-purple-100 max-w-3xl mx-auto">
                    Informasi kesehatan terkini dan berita terbaru dari SIPO
                </p>
            </div>
        </div>
    </div>

    <!-- Posts Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        @if($posts->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $post)
            <article class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover border border-gray-100">
                @if($post->image_path)
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                </div>
                @else
                <div class="h-48 bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center">
                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                @endif

                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-500 mb-3">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $post->created_at->format('d M Y') }}
                    </div>

                    <h3
                        class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 hover:text-purple-600 transition-colors">
                        <a href="{{ route('landing.posts.show', $post) }}">
                            {{ $post->title }}
                        </a>
                    </h3>

                    <div class="text-gray-600 text-sm line-clamp-3 mb-4">
                        {!! Str::limit(strip_tags($post->body), 150) !!}
                    </div>

                    <a href="{{ route('landing.posts.show', $post) }}"
                        class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium transition-colors">
                        <span>Baca Selengkapnya</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $posts->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">Belum ada artikel</h3>
            <p class="text-gray-600 text-lg">Artikel terbaru akan segera dipublikasikan</p>
        </div>
        @endif
    </div>
</div>
@endsection
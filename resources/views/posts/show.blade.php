@extends('layouts.app')

@section('page-title', 'Detail Postingan')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <div class="bg-linear-to-r from-blue-600 to-blue-700 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
            Detail Postingan
        </h1>
        <p class="text-gray-600 mt-2 ml-1">{{ $post->title }}</p>
    </div>

    <!-- Content Card -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 bg-linear-to-r from-gray-50 to-purple-50">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $post->title }}</h2>
                    <p class="text-sm text-gray-600">Slug: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{
                            $post->slug }}</span></p>
                    <p class="text-sm text-gray-600 mt-1">Dibuat: {{ $post->created_at->format('d F Y H:i') }}</p>
                    @if($post->updated_at != $post->created_at)
                    <p class="text-sm text-gray-600">Diupdate: {{ $post->updated_at->format('d F Y H:i') }}</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('posts.edit', $post) }}"
                        class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('posts.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Image -->
        @if($post->image_path)
        <div class="p-6 border-b border-gray-200">
            <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}"
                class="w-full max-w-md h-auto rounded-lg shadow-md">
        </div>
        @endif

        <!-- Content -->
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Konten Postingan</h3>
            <div class="prose prose-lg max-w-none">
                {!! $post->body !!}
            </div>
        </div>
    </div>
</div>
@endsection
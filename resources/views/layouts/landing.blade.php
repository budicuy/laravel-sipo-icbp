<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'SIPO - Sistem Informasi Pelayanan Kesehatan')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Prose styles for article content */
        .prose {
            max-width: none;
        }

        .prose p {
            margin-bottom: 1rem;
            line-height: 1.75;
        }

        .prose h1,
        .prose h2,
        .prose h3,
        .prose h4,
        .prose h5,
        .prose h6 {
            font-weight: 700;
            line-height: 1.25;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .prose h1 {
            font-size: 2.25rem;
        }

        .prose h2 {
            font-size: 1.875rem;
        }

        .prose h3 {
            font-size: 1.5rem;
        }

        .prose ul,
        .prose ol {
            margin-top: 1rem;
            margin-bottom: 1rem;
            padding-left: 2rem;
        }

        .prose li {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .prose a {
            color: #7c3aed;
            text-decoration: underline;
        }

        .prose a:hover {
            color: #6d28d9;
        }
    </style>
    @vite('resources/css/app.css')
</head>

<body class="scroll-smooth bg-gray-50">
    @yield('content')
</body>

</html>
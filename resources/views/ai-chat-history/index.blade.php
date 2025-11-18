@extends('layouts.app')

@section('title', 'Riwayat AI Chat')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Riwayat AI Chat</h1>
                <p class="text-gray-600 mt-2">Monitor dan analisis penggunaan AI Chat oleh karyawan</p>
            </div>
            <a href="{{ route('ai-chat-history.export') }}"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg inline-flex items-center gap-2 transition-all shadow-sm hover:shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-blue-600 rounded-lg shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-blue-700">Total Pengguna</p>
                    <p class="text-3xl font-bold text-blue-900">{{ number_format($statistics['total_users']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-green-600 rounded-lg shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-green-700">Total Login</p>
                    <p class="text-3xl font-bold text-green-900">{{ number_format($statistics['total_logins']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-purple-600 rounded-lg shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-purple-700">Akses AI Chat</p>
                    <p class="text-3xl font-bold text-purple-900">{{ number_format($statistics['total_ai_chat_access'])
                        }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-sm p-6 border border-orange-200">
            <div class="flex items-center justify-between">
                <div class="p-3 bg-orange-600 rounded-lg shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-orange-700">Aktif Hari Ini</p>
                    <p class="text-3xl font-bold text-orange-900">{{ number_format($statistics['active_users_today']) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Aktivitas AI Chat</h2>
                <select id="chartPeriod"
                    class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="week">7 Hari Terakhir</option>
                    <option value="month">30 Hari Terakhir</option>
                    <option value="year">12 Bulan Terakhir</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Pengguna AI Chat Teratas</h2>
            <div class="space-y-3 max-h-80 overflow-y-auto pr-2">
                @forelse ($statistics['top_users'] as $index => $user)
                <div
                    class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-white rounded-lg border border-purple-100 hover:shadow-md transition-all">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold shadow-md">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $user->nama_karyawan }}</p>
                            <p class="text-sm text-gray-600">{{ $user->nik }} • {{ $user->departemen ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($user->ai_chat_access_count) }}
                        </p>
                        <p class="text-xs text-gray-500">akses</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="font-medium">Belum ada aktivitas AI chat</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Ringkasan Aktivitas</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Aktif Minggu Ini</span>
                    <span class="font-bold text-gray-900 text-lg">{{
                        number_format($statistics['active_users_this_week']) }} pengguna</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Aktif Bulan Ini</span>
                    <span class="font-bold text-gray-900 text-lg">{{
                        number_format($statistics['active_users_this_month']) }} pengguna</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-700 font-medium">Rata-rata Akses per Pengguna</span>
                    <span class="font-bold text-gray-900 text-lg">
                        {{ $statistics['total_users'] > 0 ? number_format($statistics['total_ai_chat_access'] /
                        $statistics['total_users'], 1) : 0 }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Aktivitas Terkini</h2>
            <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                @forelse ($statistics['recent_activity'] as $activity)
                <div
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $activity->nama_karyawan }}</p>
                        <p class="text-sm text-gray-600">{{ $activity->nik }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900">{{ $activity->last_ai_chat_access_at ?
                            $activity->last_ai_chat_access_at->format('d/m H:i') : '-' }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($activity->ai_chat_access_count) }} akses</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-400">
                    <p>Tidak ada aktivitas terkini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <h2 class="text-xl font-semibold text-gray-900">Riwayat Pengguna</h2>
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Cari berdasarkan nama atau NIK..."
                    class="border border-gray-300 rounded-lg pl-10 pr-4 py-2.5 w-72 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                <svg class="w-5 h-5 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIK
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                            Departemen</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Jumlah
                            Login</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Login
                            Terakhir</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Akses
                            AI Chat</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">AI Chat
                            Terakhir</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($userHistories as $history)
                    <tr class="hover:bg-purple-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $history->nik }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{
                            $history->nama_karyawan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $history->departemen ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{
                            number_format($history->login_count) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $history->last_login_at ? $history->last_login_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-purple-600">{{
                            number_format($history->ai_chat_access_count) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $history->last_ai_chat_access_at ? $history->last_ai_chat_access_at->format('d/m/Y H:i')
                            : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('ai-chat-history.show', ['nik' => $history->nik]) }}"
                                class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="font-medium text-lg">Tidak ada riwayat pengguna ditemukan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($userHistories->hasPages())
        <div class="mt-6">
            {{ $userHistories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('activityChart').getContext('2d');
let activityChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [{
            label: 'Pengguna AI Chat',
            data: [],
            borderColor: 'rgb(147, 51, 234)',
            backgroundColor: 'rgba(147, 51, 234, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointBackgroundColor: 'rgb(147, 51, 234)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

function loadChartData(period = 'week') {
    fetch(`{{ route('api.ai-chat-history.chart-data') }}?period=${period}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                activityChart.data.labels = data.data.labels;
                activityChart.data.datasets[0].data = data.data.data;
                activityChart.update();
            }
        })
        .catch(error => console.error('Error:', error));
}

loadChartData('week');

document.getElementById('chartPeriod').addEventListener('change', function() {
    loadChartData(this.value);
});

let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();

    searchTimeout = setTimeout(() => {
        if (query.length >= 2) {
            fetch('{{ route('ai-chat-history.search') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ q: query })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) updateTableWithSearchResults(data.data);
            });
        } else if (query.length === 0) {
            location.reload();
        }
    }, 500);
});

function updateTableWithSearchResults(results) {
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = results.length === 0
        ? '<tr><td colspan="8" class="px-6 py-16 text-center text-gray-400"><svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><p class="font-medium">Tidak ada hasil ditemukan</p></td></tr>'
        : results.map(user => `
            <tr class="hover:bg-purple-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.nik}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.nama_karyawan}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${user.departemen || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${user.login_count}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${user.last_login_at || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-purple-600">${user.ai_chat_access_count}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${user.last_ai_chat_access_at || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="/ai-chat-history/${user.nik}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Lihat
                    </a>
                </td>
            </tr>
        `).join('');
}
</script>
@endpush
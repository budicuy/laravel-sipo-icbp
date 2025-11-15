@extends('layouts.app')

@section('title', 'Riwayat Token Emergency')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <div class="bg-linear-to-r from-gray-600 to-gray-700 p-3 rounded-lg shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Riwayat Token Emergency</h1>
                <p class="text-gray-600 mt-1">Riwayat penggunaan dan manajemen token emergency</p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search -->
            <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                <input type="text" id="search" placeholder="Cari token, nama pengguna..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">
            </div>

            <!-- Date Range Filter -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Dari:</label>
                <input type="date" id="dateFrom"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">
            </div>

            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Sampai:</label>
                <input type="date" id="dateTo"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">
            </div>

            <!-- Status Filter -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Status:</label>
                <select id="statusFilter"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <option value="">Semua</option>
                    <option value="available">Tersedia</option>
                    <option value="used">Digunakan</option>
                    <option value="expired">Kadaluarsa</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button id="filterBtn"
                    class="bg-linear-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium px-4 py-2 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                <button id="resetBtn"
                    class="bg-white hover:bg-gray-50 border-2 border-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset
                </button>
                <button id="exportBtn"
                    class="bg-white hover:bg-gray-50 border-2 border-green-300 text-green-700 font-medium px-4 py-2 rounded-lg transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Audit Trail Table -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <!-- Table Header -->
        <div class="bg-linear-to-r from-gray-600 to-gray-700 px-6 py-4">
            <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Riwayat
            </h2>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            No</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Token</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Status</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Pemilik</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Generator</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Dibuat</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Digunakan</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider border-r border-gray-200">
                            Catatan</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-900 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($auditTrail as $index => $token)
                    <tr class="hover:bg-gray-50 transition-colors audit-row" data-token="{{ $token->token }}"
                        data-status="{{ $token->status }}"
                        data-owner="{{ $token->user ? $token->user->nama_lengkap : '' }}"
                        data-generator="{{ $token->generator ? $token->generator->nama_lengkap : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            {{ $index + 1 }}
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-200">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $token->token }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            {!! $token->status_badge !!}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            @if($token->user)
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center mr-2">
                                    <span class="text-xs font-medium text-purple-600">
                                        {{ substr($token->user->nama_lengkap, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $token->user->nama_lengkap }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $token->user->email }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            @if($token->generator)
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-2">
                                    <span class="text-xs font-medium text-green-600">
                                        {{ substr($token->generator->nama_lengkap, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $token->generator->nama_lengkap }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $token->generator->username }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            <div>
                                <div class="text-sm">{{ $token->created_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ $token->created_at->diffForHumans() }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-200">
                            @if($token->used_at)
                            <div>
                                <div class="text-sm">{{ $token->used_at->format('d/m/Y H:i') }}</div>
                                <div class="text-xs text-gray-500">{{ $token->used_at->diffForHumans() }}</div>
                            </div>
                            @else
                            <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200 max-w-xs">
                            <div class="truncate" title="{{ $token->notes }}">
                                {{ $token->notes ?: '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($token->request_status)
                            <div class="flex items-center justify-center space-x-1">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Permintaan
                                </span>
                                {!! $token->request_status_badge !!}
                            </div>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-lg font-medium">Tidak ada data audit trail</p>
                            <p class="text-sm mt-1">Belum ada aktivitas token yang tercatat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Filter functionality
document.getElementById('filterBtn').addEventListener('click', function() {
    const searchTerm = document.getElementById('search').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;

    const rows = document.querySelectorAll('.audit-row');

    rows.forEach(row => {
        const token = row.getAttribute('data-token').toLowerCase();
        const status = row.getAttribute('data-status');
        const owner = row.getAttribute('data-owner').toLowerCase();
        const generator = row.getAttribute('data-generator').toLowerCase();

        // Get date from the row (assuming date is in the 6th column)
        const dateCell = row.querySelector('td:nth-child(6)');
        const dateText = dateCell ? dateCell.textContent.trim() : '';
        const rowDate = dateText ? new Date(dateText.split('/')[2], dateText.split('/')[1]-1, dateText.split('/')[0]) : null;

        const matchesSearch = token.includes(searchTerm) || owner.includes(searchTerm) || generator.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesDateFrom = !dateFrom || (rowDate && rowDate >= new Date(dateFrom));
        const matchesDateTo = !dateTo || (rowDate && rowDate <= new Date(dateTo));

        if (matchesSearch && matchesStatus && matchesDateFrom && matchesDateTo) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Reset filter
document.getElementById('resetBtn').addEventListener('click', function() {
    document.getElementById('search').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';

    const rows = document.querySelectorAll('.audit-row');
    rows.forEach(row => {
        row.style.display = '';
    });
});

// Export functionality
document.getElementById('exportBtn').addEventListener('click', function() {
    // Create a CSV from the visible rows
    const rows = Array.from(document.querySelectorAll('.audit-row')).filter(row => row.style.display !== 'none');

    if (rows.length === 0) {
        alert('Tidak ada data untuk diekspor');
        return;
    }

    let csv = 'No,Token,Status,Pemilik,Generator,Dibuat,Digunakan,Catatan\n';

    rows.forEach((row, index) => {
        const cells = row.querySelectorAll('td');
        const rowData = [
            index + 1,
            cells[1].textContent.trim(),
            cells[2].textContent.trim(),
            cells[3].textContent.trim(),
            cells[4].textContent.trim(),
            cells[5].textContent.trim(),
            cells[6].textContent.trim(),
            cells[7].textContent.trim()
        ];

        csv += rowData.map(cell => `"${cell}"`).join(',') + '\n';
    });

    // Create download link
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `token_audit_trail_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
});
</script>
@endsection
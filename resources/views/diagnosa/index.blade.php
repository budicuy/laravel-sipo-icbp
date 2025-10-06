@extends('layouts.app')

@section('page-title', 'Data Diagnosa')

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">DATA DIAGNOSA</h2>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('diagnosa.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Diagnosa
        </a>
        <button class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-md transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Terpilih
        </a>
        <button class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Hapus Terpilih
        </button>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Show</label>
                <select class="px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <option>50</option>
                    <option>100</option>
                    <option>200</option>
                </select>
                <span class="text-sm text-gray-700">entries</span>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Search:</label>
                <input type="text" class="px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" placeholder="">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Nama Diagnosa</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Deskripsi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Row 1 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">1</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Anemia Defisiensi Besi</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Kekurangan hemoglobin akibat defisiensi zat besi.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ route('diagnosa.edit', 1) }}" class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </a>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Row 2 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">2</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Apendisitis</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Radang usus buntu.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Row 3 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">3</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Asma Bronkial</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Penyakit pernapasan kronis akibat peradangan saluran udara.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Row 4 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">4</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Bronkitis</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Peradangan pada bronkus yang menyebabkan batuk berdahak.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Row 5 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">5</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">CAMPAK RUBELLA</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Infeksi virus campak dan rubella yang menyebabkan ruam dan demam.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Row 6 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">6</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">CEPALGIA</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Sakit kepala yang dapat disebabkan oleh berbagai kondisi, termasuk migrain dan tensi tinggi.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Row 7 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">7</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Demam Berdarah Dengue (DBD)</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Infeksi virus Dengue yang ditularkan nyamuk Aedes aegypti.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Row 8 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">8</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">DERMATITIS ALERGIKA</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Peradangan kulit akibat reaksi alergi, ditandai gatal, kemerahan, dan ruam.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Row 9 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">9</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">DIABETES MELITUS</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Penyakit metabolik dengan kadar gula darah tinggi akibat gangguan insulin.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    <!-- Row 10 -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300">
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">10</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">DIARE</td>
                        <td class="px-4 py-3 text-sm text-gray-900">Buang air besar cair atau lebih sering dari normal, biasanya akibat infeksi atau gangguan pencernaan.</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded transition-colors mr-1">
                                Edit
                            </button>
                            <button class="inline-flex items-center justify-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="text-sm text-gray-700">
                Showing 1 to 10 of 10 entries
            </div>
            <div class="flex gap-2">
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    Previous
                </button>
                <button class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition-colors">
                    1
                </button>
                <button class="px-3 py-1 border border-gray-300 rounded-md text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

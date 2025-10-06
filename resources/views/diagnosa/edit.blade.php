@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('diagnosa.index') }}" class="text-gray-600 hover:text-gray-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Edit Diagnosa</h2>
            </div>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                ID: DGN001
            </span>
        </div>

        <form action="{{ route('diagnosa.update', 1) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nama Diagnosa -->
            <div>
                <label for="nama_diagnosa" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Diagnosa <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nama_diagnosa"
                    name="nama_diagnosa"
                    value="Anemia Defisiensi Besi"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    placeholder="Masukkan nama diagnosa"
                    required
                />
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea
                    id="deskripsi"
                    name="deskripsi"
                    rows="4"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none"
                    placeholder="Masukkan deskripsi diagnosa"
                >Kekurangan hemoglobin akibat defisiensi zat besi</textarea>
            </div>

            <!-- Obat yang Direkomendasikan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Obat yang Direkomendasikan
                </label>
                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 max-h-96 overflow-y-auto">
                    <!-- Search Box -->
                    <div class="mb-4">
                        <div class="relative">
                            <input
                                type="text"
                                id="search-obat"
                                class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Cari obat..."
                                onkeyup="filterObat()"
                            />
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Select All -->
                    <div class="mb-3 pb-3 border-b border-gray-300">
                        <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded transition-colors">
                            <input
                                type="checkbox"
                                id="select-all"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                onchange="toggleAll(this)"
                            />
                            <span class="ml-3 text-sm font-semibold text-gray-900">Pilih Semua</span>
                        </label>
                    </div>

                    <!-- Obat List in 3 Columns -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2" id="obat-list">
                        <!-- Column 1 -->
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="ABC" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">ABC</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Ambroxol" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Ambroxol</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Amoxicillin" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Amoxicillin</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Azmet" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Azmet</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Cefadroxila" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Cefadroxila</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="CTM" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">CTM</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Doksisiklin / Janoprazole" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Doksisiklin / Janoprazole</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Haemopbast" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Haemopbast</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Methyl" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Methyl</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item bg-blue-100">
                                <input type="checkbox" name="obat[]" value="Neurobion/Neuropyron" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox" checked>
                                <span class="ml-3 text-sm text-gray-900 font-medium">Neurobion/Neuropyron</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Panadol Extra" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Panadol Extra</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Piroxicamp" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Piroxicamp</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="S.K Hydro" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">S.K Hydro</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Salep mata Gentamit" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Salep mata Gentamit</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Sprtie R co" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Sprtie R co</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="T. Telinga" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">T. Telinga</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Tm. Gentamit" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Tm. Gentamit</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Vermizol / salbu mg" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Vermizol / salbu mg</span>
                            </label>
                        </div>

                        <!-- Column 2 -->
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Allupurinol" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Allupurinol</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Amlodipine 10mg" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Amlodipine 10mg</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Antasid 5g" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Antasid 5g</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Attapulgite" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Attapulgite</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="CETIRIZINE" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">CETIRIZINE</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Daria" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Daria</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Flucodex. du" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Flucodex. du</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Kassa Steril" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Kassa Steril</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Microgynon" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Microgynon</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Odresteril / Biopalasipton" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Odresteril / Biopalasipton</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Paracetamol" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Paracetamol</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Ranidan" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Ranidan</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="S.K Miconazole" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">S.K Miconazole</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Simvastatin" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Simvastatin</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Stic A.U" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Stic A.U</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Temia Mata" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Temia Mata</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Tm. Imito" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Tm. Imito</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item bg-blue-100">
                                <input type="checkbox" name="obat[]" value="Vit C" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox" checked>
                                <span class="ml-3 text-sm text-gray-900 font-medium">Vit C</span>
                            </label>
                        </div>

                        <!-- Column 3 -->
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Ambeven" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Ambeven</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Amlodipine 5mg" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Amlodipine 5mg</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Artasid tab" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Artasid tab</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Cefixime" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Cefixime</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Ciprofloxacin" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Ciprofloxacin</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Diabeton" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Diabeton</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Gratikozif" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Gratikozif</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Mefforment" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Mefforment</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Nadic" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Nadic</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Panadol Biru" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Panadol Biru</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Paracetamol Sy" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Paracetamol Sy</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="S.K Gentamicin / Gencort" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">S.K Gentamicin / Gencort</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Salbutamol 4mg" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Salbutamol 4mg</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Stigma / Dernil" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Stigma / Dernil</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Stic Cho" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Stic Cho</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Terma margih / cidutatistin sy" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Terma margih / cidutatistin sy</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-blue-50 p-2 rounded transition-colors obat-item">
                                <input type="checkbox" name="obat[]" value="Tricoferol Kills Jdkn" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Tricoferol Kills Jdkn</span>
                            </label>
                        </div>
                    </div>

                    <!-- Selected Count -->
                    <div class="mt-4 pt-3 border-t border-gray-300 flex justify-between items-center">
                        <p class="text-sm text-gray-600">
                            Terpilih: <span id="selected-count" class="font-semibold text-blue-600">2</span> obat
                        </p>
                        <button type="button" onclick="clearAll()" class="text-sm text-red-600 hover:text-red-700 font-medium">
                            Hapus Semua Pilihan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button
                    type="submit"
                    class="flex-1 sm:flex-none px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                </button>
                <a
                    href="{{ route('diagnosa.index') }}"
                    class="flex-1 sm:flex-none px-8 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Update selected count
    function updateCount() {
        const checkboxes = document.querySelectorAll('.obat-checkbox:checked');
        document.getElementById('selected-count').textContent = checkboxes.length;
    }

    // Toggle all checkboxes
    function toggleAll(source) {
        const checkboxes = document.querySelectorAll('.obat-checkbox');
        checkboxes.forEach(checkbox => {
            if (checkbox.closest('.obat-item').style.display !== 'none') {
                checkbox.checked = source.checked;

                // Update visual feedback
                if (source.checked) {
                    checkbox.closest('.obat-item').classList.add('bg-blue-100');
                    checkbox.closest('.obat-item').querySelector('span').classList.add('font-medium', 'text-gray-900');
                    checkbox.closest('.obat-item').querySelector('span').classList.remove('text-gray-700');
                } else {
                    checkbox.closest('.obat-item').classList.remove('bg-blue-100');
                    checkbox.closest('.obat-item').querySelector('span').classList.remove('font-medium', 'text-gray-900');
                    checkbox.closest('.obat-item').querySelector('span').classList.add('text-gray-700');
                }
            }
        });
        updateCount();
    }

    // Clear all selections
    function clearAll() {
        const checkboxes = document.querySelectorAll('.obat-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.closest('.obat-item').classList.remove('bg-blue-100');
            checkbox.closest('.obat-item').querySelector('span').classList.remove('font-medium', 'text-gray-900');
            checkbox.closest('.obat-item').querySelector('span').classList.add('text-gray-700');
        });
        document.getElementById('select-all').checked = false;
        updateCount();
    }

    // Filter obat based on search
    function filterObat() {
        const searchText = document.getElementById('search-obat').value.toLowerCase();
        const items = document.querySelectorAll('.obat-item');

        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchText)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Add event listeners to all checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.obat-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Update visual feedback
                if (this.checked) {
                    this.closest('.obat-item').classList.add('bg-blue-100');
                    this.closest('.obat-item').querySelector('span').classList.add('font-medium', 'text-gray-900');
                    this.closest('.obat-item').querySelector('span').classList.remove('text-gray-700');
                } else {
                    this.closest('.obat-item').classList.remove('bg-blue-100');
                    this.closest('.obat-item').querySelector('span').classList.remove('font-medium', 'text-gray-900');
                    this.closest('.obat-item').querySelector('span').classList.add('text-gray-700');
                }
                updateCount();
            });
        });

        // Initial count
        updateCount();
    });
</script>
@endpush
@endsection

@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Tambah Diagnosa</h2>

        <!-- Import Section -->
        <div class="mb-8 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-l-4 border-blue-500">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Import Data Diagnosa
            </h3>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex-1 w-full">
                    <input
                        type="file"
                        id="file-import"
                        accept=".xlsx,.xls,.csv"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 file:cursor-pointer cursor-pointer"
                    />
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button
                        type="button"
                        class="flex-1 sm:flex-none px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg whitespace-nowrap"
                    >
                        Import
                    </button>
                    <button
                        type="button"
                        class="flex-1 sm:flex-none px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg whitespace-nowrap"
                    >
                        Download Template
                    </button>
                </div>
            </div>
        </div>

        <!-- Manual Form Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Tambah Manual
            </h3>
            <p class="text-sm text-gray-500 mt-1">Isi informasi diagnosa dan pilih obat yang direkomendasikan</p>
        </div>

        <form action="{{ route('diagnosa.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nama Diagnosa -->
            <div>
                <label for="nama_diagnosa" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Diagnosa <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nama_diagnosa"
                    name="nama_diagnosa"
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
                ></textarea>
            </div>

            <!-- Obat yang Direkomendasikan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Obat yang Direkomendasikan
                </label>
                <div class="border border-gray-300 rounded-lg p-4 bg-gray-50 max-h-96 overflow-y-auto">
                    <!-- Search Box -->
                    <div class="mb-4">
                        <input
                            type="text"
                            id="search-obat"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Cari obat..."
                            onkeyup="filterObat()"
                        />
                    </div>

                    <!-- Select All -->
                    <div class="mb-3 pb-3 border-b border-gray-300">
                        <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded">
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
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="ABC" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">ABC</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Ambroxol" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Ambroxol</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Amoxicillin" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Amoxicillin</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Azmet" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Azmet</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Cefadroxila" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Cefadroxila</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="CTM" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">CTM</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Doksisiklin / Janoprazole" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Doksisiklin / Janoprazole</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Haemopbast" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Haemopbast</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Methyl" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Methyl</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Neurobion/Neuropyron" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Neurobion/Neuropyron</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Panadol Extra" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Panadol Extra</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Piroxicamp" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Piroxicamp</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="S.K Hydro" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">S.K Hydro</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Salep mata Gentamit" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Salep mata Gentamit</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Sprtie R co" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Sprtie R co</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="T. Telinga" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">T. Telinga</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Tm. Gentamit" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Tm. Gentamit</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Vermizol / salbu mg" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Vermizol / salbu mg</span>
                            </label>
                        </div>

                        <!-- Column 2 -->
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Allupurinol" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Allupurinol</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Amlodipine 10mg" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Amlodipine 10mg</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Antasid 5g" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Antasid 5g</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Attapulgite" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Attapulgite</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="CETIRIZINE" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">CETIRIZINE</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Daria" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Daria</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Flucodex. du" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Flucodex. du</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Kassa Steril" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Kassa Steril</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Microgynon" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Microgynon</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Odresteril / Biopalasipton" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Odresteril / Biopalasipton</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Paracetamol" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Paracetamol</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Ranidan" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Ranidan</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="S.K Miconazole" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">S.K Miconazole</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Simvastatin" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Simvastatin</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Stic A.U" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Stic A.U</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Temia Mata" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Temia Mata</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Tm. Imito" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Tm. Imito</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Vit C" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Vit C</span>
                            </label>
                        </div>

                        <!-- Column 3 -->
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Ambeven" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Ambeven</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Amlodipine 5mg" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Amlodipine 5mg</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Artasid tab" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Artasid tab</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Cefixime" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Cefixime</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Ciprofloxacin" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Ciprofloxacin</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Diabeton" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Diabeton</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Gratikozif" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Gratikozif</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Mefforment" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Mefforment</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Nadic" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Nadic</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Panadol Biru" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Panadol Biru</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Paracetamol Sy" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Paracetamol Sy</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="S.K Gentamicin / Gencort" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">S.K Gentamicin / Gencort</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Salbutamol 4mg" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Salbutamol 4mg</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Stigma / Dernil" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Stigma / Dernil</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Stic Cho" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Stic Cho</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Terma margih / cidutatistin sy" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Terma margih / cidutatistin sy</span>
                            </label>
                            <label class="flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded obat-item">
                                <input type="checkbox" name="obat[]" value="Tricoferol Kills Jdkn" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 obat-checkbox">
                                <span class="ml-3 text-sm text-gray-700">Tricoferol Kills Jdkn</span>
                            </label>
                        </div>
                    </div>

                    <!-- Selected Count -->
                    <div class="mt-4 pt-3 border-t border-gray-300">
                        <p class="text-sm text-gray-600">
                            Terpilih: <span id="selected-count" class="font-semibold text-blue-600">0</span> obat
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button
                    type="submit"
                    class="flex-1 sm:flex-none px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan
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
            }
        });
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
            checkbox.addEventListener('change', updateCount);
        });

        // Initial count
        updateCount();
    });
</script>
@endpush
@endsection

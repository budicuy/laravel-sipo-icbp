<?php

// Simple test without Laravel framework bootstrap
// Just use the models directly

echo "=== TESTING NEW WORKFLOW: Employee Login + Family Selection ===\n\n";

// Test data
$employeeNik = '1200730';
$employeeName = 'Mastaharah';

echo "1. Testing employee login (regular NIK)...\n";
$karyawan = Karyawan::where('nik_karyawan', $employeeNik)->first();

if ($karyawan) {
    echo "✅ Employee found: {$karyawan->nama_karyawan}\n";
} else {
    echo "❌ Employee not found\n";
    exit(1);
}

echo "\n2. Testing family member selection...\n";
$familyMembers = Keluarga::where('id_karyawan', $karyawan->id_karyawan)->get();

if ($familyMembers->count() > 0) {
    echo "✅ Found {$familyMembers->count()} family members\n";

    foreach ($familyMembers as $member) {
        echo "   - {$member->nama_keluarga} (ID: {$member->id_keluarga}, Kode: {$member->kode_hubungan})\n";
    }

    // Select first family member for testing
    $selectedFamily = $familyMembers->first();
    echo "\n3. Selecting family member: {$selectedFamily->nama_keluarga}\n";

    // Simulate the new workflow: employee login + family selection
    echo "\n4. Testing new workflow...\n";

    // Build NIK-KodeHubungan format for AI Chat
    $nikForApi = "{$employeeNik}-{$selectedFamily->kode_hubungan}";
    $userNameForApi = $selectedFamily->nama_keluarga;

    echo "   Employee NIK: {$employeeNik}\n";
    echo "   Selected Family: {$selectedFamily->nama_keluarga}\n";
    echo "   Family Code: {$selectedFamily->kode_hubungan}\n";
    echo "   NIK for API: {$nikForApi}\n";
    echo "   User Name for API: {$userNameForApi}\n";

    // Test AI Chat recording
    echo "\n5. Testing AI Chat recording with new workflow...\n";
    $result = AIChatHistory::recordAIChatAccess($nikForApi);

    if ($result) {
        echo "✅ AI Chat access recorded successfully\n";
        echo "   NIK: {$result->nik}\n";
        echo "   Name: {$result->nama_karyawan}\n";
        echo "   Type: {$result->tipe_pengguna}\n";
        echo "   Kode Hubungan: {$result->kode_hubungan}\n";
        echo "   AI Chat Access Count: {$result->ai_chat_access_count}\n";
        echo "   Last AI Chat Access: {$result->last_ai_chat_access_at}\n";
    } else {
        echo "❌ Failed to record AI Chat access\n";
    }

    // Verify database record
    echo "\n6. Verifying database record...\n";
    $record = AIChatHistory::where('nik', $nikForApi)->first();

    if ($record) {
        echo "✅ Record found in database:\n";
        echo "   NIK: {$record->nik}\n";
        echo "   Nama: {$record->nama_karyawan}\n";
        echo "   Departemen: {$record->departemen}\n";
        echo "   Tipe Pengguna: {$record->tipe_pengguna}\n";
        echo "   Kode Hubungan: {$record->kode_hubungan}\n";
        echo "   AI Chat Access Count: {$record->ai_chat_access_count}\n";
        echo "   Last AI Chat Access: {$record->last_ai_chat_access_at}\n";
    } else {
        echo "❌ Record not found in database\n";
    }

} else {
    echo "❌ No family members found\n";
}

echo "\n=== NEW WORKFLOW TESTING COMPLETED ===\n";

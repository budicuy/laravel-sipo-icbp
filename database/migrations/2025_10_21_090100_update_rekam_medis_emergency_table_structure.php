<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rekam_medis_emergency', function (Blueprint $table) {
            // Drop existing columns that will be replaced
            $table->dropColumn(['nik_pasien', 'nama_pasien', 'no_rm', 'hubungan', 'jenis_kelamin']);

            // Add foreign key to external_employees
            $table->unsignedBigInteger('id_external_employee')->after('id_emergency');

            // Add relationship to keluhan table (one-to-one for emergency)
            $table->unsignedInteger('id_keluhan')->nullable()->after('catatan');

            // Update status column name to match rekam_medis
            $table->renameColumn('status_rekam_medis', 'status');

            // Drop diagnosa text column (will use relationship)
            $table->dropColumn('diagnosa');

            // Add foreign key constraints
            $table->foreign('id_external_employee')->references('id_external_employee')->on('external_employees')
                ->onUpdate('cascade')->onDelete('restrict');

            $table->foreign('id_keluhan')->references('id_keluhan')->on('keluhan')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis_emergency', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['id_external_employee']);
            $table->dropForeign(['id_keluhan']);

            // Drop added columns
            $table->dropColumn(['id_external_employee', 'id_keluhan']);

            // Add back original columns
            $table->string('nik_pasien', 16)->after('id_emergency');
            $table->string('nama_pasien')->after('nik_pasien');
            $table->string('no_rm')->after('nama_pasien');
            $table->string('hubungan')->default('Emergency')->after('no_rm');
            $table->enum('jenis_kelamin', ['L', 'P'])->after('hubungan');

            // Add back diagnosa column
            $table->text('diagnosa')->nullable()->after('waktu_periksa');

            // Rename status back
            $table->renameColumn('status', 'status_rekam_medis');
        });
    }
};

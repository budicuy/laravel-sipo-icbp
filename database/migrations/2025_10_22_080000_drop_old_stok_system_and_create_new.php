<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hapus tabel stok_bulanan lama
        Schema::dropIfExists('stok_bulanan');

        // Hapus tabel stok_obat jika sudah ada untuk menghindari error
        Schema::dropIfExists('stok_obat');

        // Buat tabel stok_obat baru dengan sistem revisi
        Schema::create('stok_obat', function (Blueprint $table) {
            $table->id('id_stok_obat');
            $table->unsignedInteger('id_obat');
            $table->string('periode', 7); // Format: MM-YY (08-24)
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_pakai')->default(0);
            $table->integer('stok_akhir')->default(0);
            $table->boolean('is_initial_stok')->default(false); // Tandai stok awal pertama kali
            $table->text('keterangan')->nullable(); // Keterangan untuk stok masuk
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable();

            // Foreign key ke tabel obat
            $table->foreign('id_obat')->references('id_obat')->on('obat')
                ->onUpdate('cascade')->onDelete('cascade');

            // Unique constraint untuk setiap obat per periode
            $table->unique(['id_obat', 'periode'], 'unique_obat_periode');

            // Index untuk performance
            $table->index('periode');
            $table->index('is_initial_stok');
        });

        // Buat procedure untuk menghitung stok pakai otomatis dari tabel keluhan
        // Hapus procedure jika sudah ada untuk menghindari error
        DB::unprepared('DROP PROCEDURE IF EXISTS calculate_stok_pakai');

        DB::unprepared('
            CREATE PROCEDURE calculate_stok_pakai(IN p_periode VARCHAR(7), IN p_id_obat INT)
            BEGIN
                DECLARE total_pakai INT DEFAULT 0;

                -- Hitung total stok pakai dari tabel keluhan berdasarkan periode dan id_obat
                SELECT COALESCE(SUM(jumlah_obat), 0) INTO total_pakai
                FROM keluhan k
                JOIN rekam_medis r ON k.id_rekam = r.id_rekam
                WHERE k.id_obat = p_id_obat
                AND DATE_FORMAT(r.tanggal_periksa, "%m-%y") = p_periode;

                -- Update stok_pakai di tabel stok_obat
                UPDATE stok_obat
                SET stok_pakai = total_pakai,
                    stok_akhir = stok_awal + stok_masuk - total_pakai
                WHERE id_obat = p_id_obat AND periode = p_periode;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus procedure
        DB::unprepared('DROP PROCEDURE IF EXISTS calculate_stok_pakai');

        // Hapus tabel stok_obat baru
        Schema::dropIfExists('stok_obat');

        // Buat kembali tabel stok_bulanan lama
        Schema::create('stok_bulanan', function (Blueprint $table) {
            $table->id('id_stok_bulanan');
            $table->unsignedInteger('id_obat');
            $table->string('periode', 7); // Format: MM-YY (08-24)
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_pakai')->default(0);
            $table->integer('stok_akhir')->default(0);
            $table->integer('stok_masuk')->default(0);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrentOnUpdate()->nullable();

            // Foreign key ke tabel obat
            $table->foreign('id_obat')->references('id_obat')->on('obat')
                ->onUpdate('cascade')->onDelete('cascade');

            // Unique constraint untuk setiap obat per periode
            $table->unique(['id_obat', 'periode'], 'unique_obat_periode');

            // Index untuk performance
            $table->index('periode');
        });
    }
};

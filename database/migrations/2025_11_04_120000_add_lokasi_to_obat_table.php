<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: Be careful running migrations in production. Review and run manually.
     */
    public function up()
    {
        if (! Schema::hasColumn('obat', 'lokasi')) {
            Schema::table('obat', function (Blueprint $table) {
                $table->string('lokasi')->nullable()->after('keterangan');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('obat', 'lokasi')) {
            Schema::table('obat', function (Blueprint $table) {
                $table->dropColumn('lokasi');
            });
        }
    }
};

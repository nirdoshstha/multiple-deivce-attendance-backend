<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

     public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('company_device_id')
                ->nullable()
                ->after('staff_id')
                ->constrained('companies_devices')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_device_id');
        });
    }
};

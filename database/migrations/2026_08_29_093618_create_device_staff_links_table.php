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
        Schema::create('device_staff_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_device_id')->constrained('companies_devices')->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('staffs')->cascadeOnDelete();
            $table->string('device_user_id'); // the uid/PIN as enrolled on the device
            $table->timestamps();

            // One staff member can only be enrolled once per device, and one
            // device-side id can only map to one staff member per device.
            $table->unique(['company_device_id', 'device_user_id']);
            $table->unique(['company_device_id', 'staff_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_staff_links');
    }
};

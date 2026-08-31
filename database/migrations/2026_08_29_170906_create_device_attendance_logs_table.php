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
        Schema::create('device_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_device_id')->constrained('companies_devices')->cascadeOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('staffs')->nullOnDelete();
            $table->string('device_user_id');   // raw id/PIN reported by the device
            $table->timestamp('punch_time');
            $table->unsignedTinyInteger('verify_type')->nullable(); // 1=fingerprint,4=card,...
            $table->unsignedTinyInteger('punch_state')->nullable(); // check-in/out/overtime etc.
            $table->boolean('processed')->default(false); // has this been folded into `attendances`?
            $table->timestamps();

            // Prevents the same physical punch being inserted twice on re-sync.
            $table->unique(
                ['company_device_id', 'device_user_id', 'punch_time'],
                'device_logs_unique_punch'
            );
            $table->index(['staff_id', 'processed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_attendance_logs');
    }
};

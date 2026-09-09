<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies_devices', function (Blueprint $table) {
            // 'pull' = your server opens a socket to the device's ip:port
            //          (only works when the device is on a reachable network).
            // 'push' = the device calls YOUR server (iClock/ADMS protocol) —
            //          works from anywhere the device has internet access.
            $table->enum('connection_mode', ['pull', 'push'])
                ->default('pull')
                ->after('status');

            // For push devices "online" isn't a live socket state — it's
            // "checked in recently." This timestamp is what that's based on.
            $table->timestamp('last_seen_at')->nullable()->after('connection_mode');
        });
    }

    public function down(): void
    {
        Schema::table('companies_devices', function (Blueprint $table) {
            $table->dropColumn(['connection_mode', 'last_seen_at']);
        });
    }
};

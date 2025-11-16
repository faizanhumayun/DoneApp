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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('calendar_sync_enabled')->default(false)->after('two_factor_confirmed_at');
            $table->string('calendar_provider')->nullable()->after('calendar_sync_enabled');
            $table->text('calendar_access_token')->nullable()->after('calendar_provider');
            $table->text('calendar_refresh_token')->nullable()->after('calendar_access_token');
            $table->timestamp('calendar_token_expires_at')->nullable()->after('calendar_refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'calendar_sync_enabled',
                'calendar_provider',
                'calendar_access_token',
                'calendar_refresh_token',
                'calendar_token_expires_at',
            ]);
        });
    }
};

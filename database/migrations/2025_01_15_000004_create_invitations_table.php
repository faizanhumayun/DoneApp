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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('invited_email');
            $table->foreignId('invited_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('invite_token')->unique();
            $table->timestamp('invite_token_expires_at');
            $table->string('status', 50)->default('pending');
            $table->timestamps();

            $table->index('invite_token');
            $table->index(['company_id', 'invited_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};

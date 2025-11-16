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
        Schema::create('guest_invites', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('token')->unique();
            $table->timestamp('token_expires_at');
            $table->foreignId('invited_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->text('personal_message')->nullable();

            // Optional: track what invited them (task, discussion, or manual)
            $table->string('invited_from_type')->nullable(); // 'task', 'discussion', 'manual'
            $table->unsignedBigInteger('invited_from_id')->nullable(); // ID of task or discussion

            $table->boolean('is_accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();

            $table->timestamps();

            $table->index(['email', 'company_id']);
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guest_invites');
    }
};

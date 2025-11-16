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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('recipient');
            $table->string('subject');
            $table->string('type')->nullable()->comment('guest-invite, task-comment, etc.');
            $table->enum('status', ['sent', 'delivered', 'failed', 'bounced'])->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();

            // Index for faster queries
            $table->index(['company_id', 'sent_at']);
            $table->index('recipient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};

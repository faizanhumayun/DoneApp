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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable'); // attachable_id, attachable_type (polymorphic)
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('original_name');
            $table->string('file_name'); // Stored filename (hashed)
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size'); // In bytes
            $table->string('storage_disk')->default('local'); // 'local' or 'spaces'
            $table->string('hash')->nullable(); // File hash for duplicate detection
            $table->timestamps();

            // Index for uploaded_by
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};

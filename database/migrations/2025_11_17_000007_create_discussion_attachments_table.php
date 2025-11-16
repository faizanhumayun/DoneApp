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
        Schema::create('discussion_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discussion_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('discussion_comment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('filename');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            $table->timestamps();

            $table->index('discussion_id');
            $table->index('discussion_comment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discussion_attachments');
    }
};

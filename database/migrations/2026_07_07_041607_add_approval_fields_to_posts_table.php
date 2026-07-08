<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending', 'published'])->default('draft')->after('content');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete()->after('status');
            $table->unsignedBigInteger('jemaat_id')->nullable()->after('author_id');
            $table->unsignedBigInteger('klasis_id')->nullable()->after('jemaat_id');
            $table->text('rejection_note')->nullable()->after('klasis_id'); // Catatan jika berita ditolak Bidang 4
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn(['status', 'author_id', 'jemaat_id', 'klasis_id', 'rejection_note']);
        });
    }
};
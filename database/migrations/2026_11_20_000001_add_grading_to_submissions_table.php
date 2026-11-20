<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->decimal('score', 5, 2)->nullable()->after('file_size');
            $table->text('feedback')->nullable()->after('score');
            $table->timestamp('graded_at')->nullable()->after('feedback');
            $table->string('graded_by')->nullable()->after('graded_at');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['score', 'feedback', 'graded_at', 'graded_by']);
        });
    }
};

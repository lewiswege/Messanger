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
        Schema::table('webhooks', function (Blueprint $table) {
            $table->string('status')->default('pending')->index()->after('payload');
            $table->text('error_log')->nullable()->after('status');
            $table->unsignedInteger('attempts')->default(0)->after('error_log');
            $table->timestamp('processed_at')->nullable()->after('attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webhooks', function (Blueprint $table) {
            $table->dropColumn(['status', 'error_log', 'attempts', 'processed_at']);
        });
    }
};

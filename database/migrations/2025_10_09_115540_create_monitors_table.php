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
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->integer('check_interval_minutes')->default(2);
            $table->enum('status', ['up', 'down', 'unknown'])->default('unknown');
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_up_at')->nullable();
            $table->timestamp('last_down_at')->nullable();
            $table->integer('response_time')->nullable(); // in milliseconds
            $table->integer('http_status_code')->nullable();
            $table->text('error_message')->nullable();
            $table->boolean('notify_on_failure')->default(true);
            $table->boolean('notify_on_recovery')->default(true);
            $table->integer('consecutive_failures')->default(0);
            $table->integer('max_consecutive_failures')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['status', 'is_active']);
            $table->index('last_checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};

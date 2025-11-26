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
            $table->boolean('is_active')->default(true);
            $table->integer('check_interval_minutes')->default(2);
            $table->integer('max_consecutive_failures')->default(3);
            $table->integer('consecutive_failures')->default(0);
            $table->enum('status', ['up', 'down', 'unknown'])->default('unknown');
            $table->boolean('check_ssl_certificate')->default(true);
            $table->timestamp('ssl_certificate_expires_at')->nullable();
            $table->boolean('notify_on_failure')->default(true);
            $table->boolean('notify_on_recovery')->default(true);
            $table->timestamps();

            $table->index('status');
            $table->index('is_active');
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

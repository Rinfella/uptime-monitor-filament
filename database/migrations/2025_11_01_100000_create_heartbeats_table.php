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
        Schema::create('heartbeats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitor_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['up', 'down', 'unknown'])->default('unknown');
            $table->integer('http_status_code')->nullable();
            $table->integer('response_time')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('checked_at')->index();
            $table->timestamps();


            $table->index(['monitor_id', 'status']);
            $table->index('monitor_id', 'checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heartbeats');
    }
};

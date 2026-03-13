<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method', 10);
            $table->string('url');
            $table->string('route')->nullable();
            $table->unsignedSmallInteger('status');
            $table->unsignedInteger('response_time_ms');
            $table->string('ip', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->nullableMorphs('user');
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};

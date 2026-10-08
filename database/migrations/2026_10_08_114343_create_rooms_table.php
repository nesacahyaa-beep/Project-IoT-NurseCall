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
        Schema::create('rooms', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();      // dikirim ESP32, mis. "R101"
            $t->string('name');
            $t->decimal('temperature', 5, 2)->nullable();
            $t->decimal('humidity', 5, 2)->nullable();
            $t->timestamp('last_seen_at')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

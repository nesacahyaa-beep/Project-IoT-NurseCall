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
        Schema::create('calls', function (Blueprint $t) {
            $t->id();
            $t->foreignId('room_id')->constrained()->cascadeOnDelete();
            $t->string('level')->default('normal');     // normal | emergency
            $t->string('status')->default('waiting');   // waiting | accepted | completed
            $t->timestamp('called_at');
            $t->timestamp('accepted_at')->nullable();
            $t->timestamp('completed_at')->nullable();
            $t->unsignedInteger('response_seconds')->nullable();
            $t->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
            $t->index(['room_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};

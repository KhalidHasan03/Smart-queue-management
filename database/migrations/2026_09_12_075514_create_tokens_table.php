<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->date('token_date')->index();
            $table->unsignedInteger('seq');
            $table->string('token_no', 20);
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('waiting')->index();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['token_date', 'service_id', 'seq']);
            $table->unique(['token_date', 'token_no']);
            $table->index(['token_date', 'status', 'service_id', 'counter_id', 'seq']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};

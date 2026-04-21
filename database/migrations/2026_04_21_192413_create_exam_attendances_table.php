<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('student_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('checked_by')
                ->constrained('teachers')
                ->onDelete('cascade');

            $table->enum('status', ['present', 'absent']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attendances');
    }
};

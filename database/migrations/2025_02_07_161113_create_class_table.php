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
        Schema::create('class', function (Blueprint $table) {
					$table->id('classID');
					$table->string('activityID');
					$table->string('teacherID');
					$table->string('studentID');
					$table->string('annID');
					$table->string('conID');
					$table->string('className');
					$table->string('classDesc');
					$table->string('classCode');
					$table->string('students');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class');
    }
};

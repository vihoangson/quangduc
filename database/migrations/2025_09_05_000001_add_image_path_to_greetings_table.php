<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('greetings', function (Blueprint $table) {
            $table->string('image_path', 255)->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('greetings', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};


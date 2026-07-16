<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('location_updates', function (Blueprint $table) {
            $table->string('operator')->nullable()->after('ip_address');
        });
    }

    public function down()
    {
        Schema::table('location_updates', function (Blueprint $table) {
            $table->dropColumn('operator');
        });
    }
};

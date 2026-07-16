<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('location_updates', function (Blueprint $table) {
            $table->string('public_ip')->nullable()->after('ip_address');
            $table->string('imei')->nullable()->after('public_ip');
        });
    }

    public function down()
    {
        Schema::table('location_updates', function (Blueprint $table) {
            $table->dropColumn(['public_ip', 'imei']);
        });
    }
};

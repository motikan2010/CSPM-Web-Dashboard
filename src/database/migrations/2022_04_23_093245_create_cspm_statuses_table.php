<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cspm_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('cloud_id');
            $table->string('exec_date');
            $table->unsignedSmallInteger('response_status_code');
            $table->unsignedTinyInteger('status');
            $table->timestamps();

            $table->unique(['cloud_id', 'exec_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cspm_statuses');
    }
};

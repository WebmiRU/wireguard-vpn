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
        Schema::create('client', function (Blueprint $table) {
            $table->id();
            $table->text('ip4')->unique();
            $table->text('key_private')->nullable()->index();
            $table->text('key_public')->nullable()->index();
            $table->text('is_granted')->nullable()->default(0)->index();
            $table->text('handshake_at')->nullable()->index();
            $table->text('active_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client');
    }
};

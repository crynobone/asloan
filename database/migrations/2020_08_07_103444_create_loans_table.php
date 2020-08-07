<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->index();
            $table->text('description')->nullable();

            $table->unsignedTinyInteger('repayment_interval')->default(7);

            $table->string('currency')->default(\config('app.currency'));
            $table->unsignedInteger('amount');

            $table->unsignedInteger('due_amount')->default(0);
            $table->timestamp('due_at')->nullable();

            $table->timestamp('term_started_at');
            $table->timestamp('term_ended_at');
            $table->timestamp('completed_at')->nullable();

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
        Schema::dropIfExists('loans');
    }
}

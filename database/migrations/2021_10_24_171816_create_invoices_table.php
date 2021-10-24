<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->integer('id', true);
            $table->tinyInteger('creditor_company_id')->nullable();
            $table->tinyInteger('debtor_company_id')->nullable();
            $table->date('invoice_date')->nullable();
            $table->float('invoice_amount', 10)->nullable();
            $table->enum('status', ['PENDING', 'COMPLETED'])->nullable()->default('PENDING');
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}

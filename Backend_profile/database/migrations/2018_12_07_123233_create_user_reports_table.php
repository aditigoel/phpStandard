<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_reports', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('reported_by')->index('reported_by');
			$table->integer('reported_to')->index('reported_to');
			$table->string('reason');
			$table->text('comment', 65535);
			$table->bigInteger('created_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_reports');
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pro_jobs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->index('user_id');
			$table->string('title');
			$table->string('slug');
			$table->string('description', 500);
			$table->bigInteger('start_date_time');
			$table->bigInteger('end_date_time');
			$table->integer('status')->default(1);
			$table->bigInteger('created_at');
			$table->bigInteger('updated_at');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('pro_jobs');
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProJobApplicationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pro_job_applications', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('pro_job_id')->index('pro_job_id');
			$table->integer('user_id')->index('user_id_2');
			$table->integer('status')->default(1)->comment('1 :shortlist, 2 : hired,');
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
		Schema::drop('pro_job_applications');
	}

}

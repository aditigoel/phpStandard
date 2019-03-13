<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProJobFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pro_job_files', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('pro_job_id')->index('pro_job_id');
			$table->string('file_name');
			$table->string('name');
			$table->string('type');
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
		Schema::drop('pro_job_files');
	}

}

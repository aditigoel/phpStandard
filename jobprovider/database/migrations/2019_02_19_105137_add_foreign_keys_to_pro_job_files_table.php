<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProJobFilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pro_job_files', function(Blueprint $table)
		{
			$table->foreign('pro_job_id', 'fk_with_pro_jobs3')->references('id')->on('pro_jobs')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pro_job_files', function(Blueprint $table)
		{
			$table->dropForeign('fk_with_pro_jobs3');
		});
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProJobSkillsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pro_job_skills', function(Blueprint $table)
		{
			$table->foreign('pro_job_id', 'fk_with_pro_jobs2')->references('id')->on('pro_jobs')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('skill_id', 'fk_with_skills')->references('id')->on('skills')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pro_job_skills', function(Blueprint $table)
		{
			$table->dropForeign('fk_with_pro_jobs2');
			$table->dropForeign('fk_with_skills');
		});
	}

}

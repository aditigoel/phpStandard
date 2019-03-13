<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSkillsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_skills', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->index('fk_consultant_professional_skills_2_idx');
			$table->integer('skill_id')->index('fk_consultant_professional_skills_1_idx');
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
		Schema::drop('user_skills');
	}

}

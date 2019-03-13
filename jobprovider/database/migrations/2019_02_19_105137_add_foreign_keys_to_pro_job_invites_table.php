<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProJobInvitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('pro_job_invites', function(Blueprint $table)
		{
			$table->foreign('pro_job_id', 'fk_with_pro_jobs4')->references('id')->on('pro_jobs')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'fk_with_user3')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('pro_job_invites', function(Blueprint $table)
		{
			$table->dropForeign('fk_with_pro_jobs4');
			$table->dropForeign('fk_with_user3');
		});
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProJobInvitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pro_job_invites', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('pro_job_id')->index('pro_job_id');
			$table->integer('user_id')->index('user_id');
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
		Schema::drop('pro_job_invites');
	}

}

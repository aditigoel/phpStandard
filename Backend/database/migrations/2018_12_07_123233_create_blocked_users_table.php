<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlockedUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('blocked_users', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('blocked_by')->index('blocked_by_2');
			$table->integer('blocked_to')->index('blocked_to_2');
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
		Schema::drop('blocked_users');
	}

}

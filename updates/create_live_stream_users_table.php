<?php namespace Pi\Livestream\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateLiveStreamUsersTable Migration
 *
 * @link https://docs.octobercms.com/3.x/extend/database/structure.html
 */
return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        // Kiểm tra table có tồn tại hay không và tạo table nếu không tồn tại
        if (!Schema::hasTable("pi_livestream_users")) {
            Schema::create("pi_livestream_users", function (
                Blueprint $table
            ) {
                $table->id();

                $table->bigInteger("user_id")->nullable();
                $table->string("host_identity", 255)->nullable();

                $table->string("agora_token", 255)->nullable();

                $table->string("status", 255)->default("active");
                $table->jsonb("joined_users")->nullable();

                $table->integer("watching_count")->default(0);
                $table->integer("comment_count")->default(0);
                $table->integer("collected_diamond")->default(0);

                $table->timestamps();
            });
        }
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('pi_livestream_users');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachment_index', function (Blueprint $table) {
            $table->char('id', 32);
            $table->string('driver', 16);
            $table->string('name', 50);
            $table->string('path', 255);
            $table->string('mime', 100);
            $table->string('extension', 10);
            $table->unsignedInteger('size')->default(0);
            $table->string('md5', 32);
            $table->string('sha1', 40);
            $table->boolean('status');
            $table->ipAddress('created_ip')->nullable();
            $table->unsignedInteger('created_port')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->ipAddress('updated_ip')->nullable();
            $table->unsignedInteger('updated_port')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachment_index');
    }
}

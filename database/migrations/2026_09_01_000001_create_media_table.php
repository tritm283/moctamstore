<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('media',function(Blueprint $t){$t->increments('media_id');$t->string('drive_file_id')->unique();$t->text('drive_view_url');$t->string('file_name');$t->string('mime_type')->nullable();$t->unsignedBigInteger('size_bytes')->nullable();$t->timestamps();});} public function down():void{Schema::dropIfExists('media');}};

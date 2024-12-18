<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGalleryPostsTable extends Migration
{
    /**
     * 테이블을 생성하는 방법.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_posts', function (Blueprint $table) {
            $table->id(); // 게시글 ID (자동 증가하는 primary key)
            $table->foreignId('gallery_id')->constrained()->onDelete('cascade'); // 갤러리 ID (foreign key)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 글쓴유저 ID (foreign key)
            $table->string('user_name'); // 글쓴유저 유저네임
            $table->text('content'); // 글 내용 (HTML 포함 가능)
            $table->integer('views')->default(0); // 조회수 (기본값 0)
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * 테이블을 삭제하는 방법.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gallery_posts');
    }
}

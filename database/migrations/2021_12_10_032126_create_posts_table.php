<?php
//criando a migrate de posts

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            //atributos da tabela de posts
            $table->increments('id'); //esse é o id auto incrementado
            $table->unsignedBigInteger('user_id'); //assim criamos a variável que faz relacionamento
            $table->text('title');
            $table->longText('description');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users'); //aqui declaramos a relação estrangeira com outra migration  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
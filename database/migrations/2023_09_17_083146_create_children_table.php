<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->nullable();
            $table->foreignId('spouse_id')->nullable();
            $table->string('name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['not_selected', 'male', 'female']);
            $table->string('passport_number')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('birth_date_fa')->nullable();
            $table->date('birth_date_en')->nullable();
            $table->string('birth_country')->nullable();
            $table->string('birth_city')->nullable();
            $table->string('birth_city_en')->nullable();
            $table->string('citizenship_country')->nullable();
            $table->string('passport_image')->nullable();
            $table->string('face_image')->nullable();
            $table->enum('face_image_status', ['not_selected', 'accepted', 'rejected', 'cropped']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('children');
    }
}

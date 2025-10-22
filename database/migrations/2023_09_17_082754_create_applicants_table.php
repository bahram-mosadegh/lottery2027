<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('registration_type', ['online', 'onsite', 'agent']);
            $table->enum('gender', ['not_selected', 'male', 'female']);
            $table->enum('marital', ['single', 'married']);
            $table->enum('marital_status', ['not_selected', 'unmarried', 'married_us_citizen', 'married_not_us_citizen', 'divorced', 'widowed']);
            $table->string('passport_number')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('birth_date_fa')->nullable();
            $table->date('birth_date_en')->nullable();
            $table->enum('education_degree', [
                'not_selected',
                'primary_school_only',
                'high_school_no_degree',
                'high_school_degree',
                'vocational_school',
                'some_university_courses',
                'university_degree',
                'some_graduate_level_courses',
                'masters_degree',
                'doctorate_level_courses',
                'doctorate_degree',
            ]);
            $table->string('birth_country')->nullable();
            $table->string('birth_city')->nullable();
            $table->string('birth_city_en')->nullable();
            $table->string('citizenship_country')->nullable();
            $table->string('residence_country')->nullable();
            $table->string('residence_state')->nullable();
            $table->string('residence_state_en')->nullable();
            $table->string('residence_city')->nullable();
            $table->string('residence_city_en')->nullable();
            $table->string('residence_street')->nullable();
            $table->string('residence_alley')->nullable();
            $table->string('residence_no')->nullable();
            $table->string('residence_unit')->nullable();
            $table->string('residence_postal_code')->nullable();
            $table->string('residence_address_en')->nullable();
            $table->string('mobile')->unique();
            $table->string('email')->nullable();
            $table->string('passport_image')->nullable();
            $table->string('face_image')->nullable();
            $table->enum('face_image_status', ['not_selected', 'accepted', 'rejected', 'cropped']);
            $table->integer('adult_children_count')->default(0);
            $table->integer('children_count')->default(0);
            $table->tinyInteger('double_register')->default(0);
            $table->string('registration_tracking_number')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->enum('payment_status', ['unpaid', 'paid']);
            $table->string('nilgam_pay_ref')->nullable();
            $table->enum('sms_status', ['not_sent', 'success', 'fail']);
            $table->string('crm_guid')->nullable()->unique();
            $table->enum('lottery_status', ['not_checked', 'not_selected', 'selected']);
            $table->string('lottery_status_sys')->nullable();
            $table->enum('lottery_status_sms', ['not_sent', 'success', 'fail']);
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
        Schema::dropIfExists('applicants');
    }
}

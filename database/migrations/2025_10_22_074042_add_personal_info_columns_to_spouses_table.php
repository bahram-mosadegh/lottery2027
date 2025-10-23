<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonalInfoColumnsToSpousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spouses', function (Blueprint $table) {
            $table->json('continents_visited')->nullable()->after('education_degree');
            $table->boolean('had_foreign_trip')->nullable()->after('education_degree');
            $table->string('job')->nullable()->after('education_degree');
            $table->enum('passport_image_status', ['not_selected', 'accepted', 'rejected'])->after('passport_image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('spouses', function (Blueprint $table) {
            $table->dropColumn('continents_visited');
            $table->dropColumn('had_foreign_trip');
            $table->dropColumn('job');
            $table->dropColumn('passport_image_status');
        });
    }
}

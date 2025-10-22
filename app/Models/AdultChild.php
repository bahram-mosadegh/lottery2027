<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdultChild extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_id',
        'spouse_id',
        'name',
        'last_name',
        'gender',
        'passport_number',
        'expire_date',
        'birth_date_fa',
        'birth_date_en',
        'education_degree',
        'birth_country',
        'birth_city',
        'citizenship_country',
        'mobile',
        'email',
        'passport_image',
        'face_image',
        'face_image_status',
        'independent_register',
        'registration_tracking_number',
        'lottery_status',
        'lottery_status_sys',
        'lottery_status_sms',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}

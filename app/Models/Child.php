<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
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
        'birth_country',
        'birth_city',
        'citizenship_country',
        'passport_image',
        'face_image',
        'face_image_status',
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}

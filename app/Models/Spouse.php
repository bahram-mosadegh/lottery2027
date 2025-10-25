<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'continents_visited' => 'array'
    ];

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }
}

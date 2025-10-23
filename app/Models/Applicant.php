<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'continents_visited' => 'array'
    ];

    public function spouse()
    {
        return $this->hasOne(Spouse::class);
    }

    public function adult_children()
    {
        return $this->hasMany(AdultChild::class);
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }
}

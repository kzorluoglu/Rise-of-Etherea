<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'cost',
        'construction_time',
        'requirements',
    ];

    protected $casts = [
        'requirements' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_buildings');
    }
}

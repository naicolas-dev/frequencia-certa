<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = ['code', 'name', 'description', 'icon', 'category', 'is_secret'];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('earned_at');
    }
}

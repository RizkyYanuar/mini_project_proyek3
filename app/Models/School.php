<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\MealDistribution;

class School extends Model
{
    protected $primaryKey = 'id_school';

    protected $fillable = ['name', 'npsn', 'address', 'district'];

    public function distributions(): HasMany
    {
        return $this->hasMany(MealDistribution::class, 'id_school', 'id_school');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'id_school', 'id_school');
    }
}
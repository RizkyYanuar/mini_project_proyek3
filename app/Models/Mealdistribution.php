<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MealDistribution extends Model
{
    protected $primaryKey = 'id_distribution';

    protected $fillable = ['id_school', 'distribution_date', 'menu_description', 'portion_count'];

    protected function casts(): array
    {
        return ['distribution_date' => 'date'];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'id_school', 'id_school');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'id_distribution', 'id_distribution');
    }
}
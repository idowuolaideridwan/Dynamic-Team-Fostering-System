<?php

namespace App\Models\API\V1\Grade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'credit_value', 'semester'
    ];

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}

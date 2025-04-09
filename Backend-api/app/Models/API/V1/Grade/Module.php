<?php

namespace App\Models\API\V1\Grade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\ModuleFactory;

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

    protected static function newFactory()
{
    return ModuleFactory::new();
}
}

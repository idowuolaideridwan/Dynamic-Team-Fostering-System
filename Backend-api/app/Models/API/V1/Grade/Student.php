<?php

namespace App\Models\API\V1\Grade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\StudentFactory;

class Student extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return StudentFactory::new();
    }
    
    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'dob'
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}

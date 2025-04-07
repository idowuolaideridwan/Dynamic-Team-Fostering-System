<?php

namespace App\Models\API\V1\Grade;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'email', 'phone', 'gender', 'address', 'enrollment_year'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

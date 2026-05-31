<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes; // नियम २.१ अनुसार पुरानो डाटा जोगाउन SoftDeletes प्रयोग गरिएको छ

    protected $fillable = [
        'user_id',
        'name',
        'designation',
        'department',
        'ot_rate',
        'is_active',
        'user_id' => 0,
    ];

    /**
     * यो कर्मचारी कुन प्रयोगकर्ता (User) सँग सम्बन्धित छ भनेर जोडिएको सम्बन्ध (Relationship)
     */
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function overtimeRecords()
{
    return $this->hasMany(OvertimeRecord::class, 'employee_id');
}

}
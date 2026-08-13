<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
    'employee_id',
    'event_id',
    'purpose_id',
    'ot_date',
    'from_time',
    'to_time',
    'total_hours',
    'designation_snapshot',
    'ot_rate_snapshot',
    'tiffin_amount',
    'is_holiday',
    'type',
    'status',
    'remarks',
    'verified_by',
    'verified_at',
    'rejection_reason',
    'rejected_by',
    'rejected_at',
];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class, 'event_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    public function rejecter()
{
    return $this->belongsTo(User::class, 'rejected_by');
}
public function purpose()
{
    return $this->belongsTo(Purpose::class);
}
}
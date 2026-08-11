<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // <--- यो थप्नुहोस्

class OvertimeRecord extends Model
{
    use HasFactory, SoftDeletes; // <--- यहाँ SoftDeletes थप्नुहोस्

    protected $fillable = [
        'employee_id',
        'event_id',
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
        'ot_rate'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function event()
{
    return $this->belongsTo(\App\Models\Event::class, 'event_id');
}
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event_name',
        'department',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'is_tiffin_eligible',
        'approver_employee_id',
        'recommender_employee_id',
    ];

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_employee_id');
    }
    public function recommender()
{
    return $this->belongsTo(\App\Models\Employee::class, 'recommender_employee_id');
}
}
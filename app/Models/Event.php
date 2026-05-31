<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['event_name', 'department', 'start_date', 'end_date', 'start_time', 'end_time', 'is_tiffin_eligible'];
}
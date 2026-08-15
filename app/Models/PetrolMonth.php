<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PetrolMonth extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = "petrol_months";
    protected $fillable = ['month', 'year', 'status'];

    public function bills()
    {
        return $this->hasMany(PetrolBill::class, 'petrol_month_id');
    }
}
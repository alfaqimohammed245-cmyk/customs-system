<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'national_id', 'nationality', 'phone_number'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_id',
        'name',
        'account_number',
    ];

    public function bank() {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }

    public function funds() {
        return $this->hasMany(Funding::class, 'bank_account_id', 'id');
    }
}

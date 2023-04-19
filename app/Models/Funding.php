<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funding extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_account_id',
        'amount',
        'unique_amount',
        'status',
        'status_updated_at',
        'note',
        'payment_slip'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function bank_account() {
        return $this->belongsTo(BankAccount::class, 'bank_account_id', 'id');
    }

    public function getPaymentPictAttribute() {
        return $this->payment_slip ? asset('storage/payments/' . $this->payment_slip) : 'https://dummyimage.com/300x300/808080/000000.png&text=Belum+ada+bukti+pembayaran';
    }
}

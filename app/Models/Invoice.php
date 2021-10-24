<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Invoice extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'creditor_company_id',
        'debtor_company_id',
        'invoice_date',
        'invoice_amount',
        'status'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}

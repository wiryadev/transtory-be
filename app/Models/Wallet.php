<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id',
        'banks_id',
        'account_no',
    ];

    /**
     * Relation: each Wallet belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation: each Wallet associated to only 1 Bank account
     */
    public function bank()
    {
        return $this->hasOne(Bank::class);
    }
    

}

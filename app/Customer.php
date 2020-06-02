<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'password',
        'last_login',
        'verify_code',
        'verified_at'
    ];

    protected $hidden = [
        'user_id',
    ];

    public function address() {
        return $this->hasOne(Address::class);
    }
}

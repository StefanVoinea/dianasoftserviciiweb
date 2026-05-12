<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etransporttokens extends Model
{
    protected $table = "etransporttokens";
    protected $fillable = [
        "cui",
        "access_token",
        "refresh_token",
        "data_obtinerii",
        "data_expirare",
        "company_id"
    ];

    /**
     * Relationship with Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
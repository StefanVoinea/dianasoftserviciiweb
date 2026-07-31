<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

class VectorSpv extends Model
{
    use ApartineCompaniei;

    protected $table = 'vector_spv';

    protected $guarded = [];

    protected $casts = [
        'data_vector' => 'date',
        'data_inceput' => 'date',
        'data_sfarsit' => 'date',
    ];
}

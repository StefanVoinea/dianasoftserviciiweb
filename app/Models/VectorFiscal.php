<?php

namespace App\Models;

use App\Models\Concerns\ApartineCompaniei;
use Illuminate\Database\Eloquent\Model;

class VectorFiscal extends Model
{
    use ApartineCompaniei;

    protected $table = 'vector_fiscal';

    protected $guarded = [];

    public const DECLARATII = ['D112', 'D300', 'D301', 'D394', 'D100', 'D101', 'D390', 'D205', 'D200', 'BILANT', 'A4200'];

    public const PERIODICITATI = ['Lunar', 'Trimestrial', 'Semestrial', 'Anual'];
}

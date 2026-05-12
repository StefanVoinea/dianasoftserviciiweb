<?php

namespace App\Imports;

use App\Models\Etransporttokens;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EtransporttokensImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Etransporttokens([
            'cui' => $row['cui'],
            'access_token' => $row['access_token'] ?? null,
            'refresh_token' => $row['refresh_token'] ?? null,
            'data_obtinerii' => $row['data_obtinerii'] ?? null,
            'data_expirare' => $row['data_expirare'] ?? null,
            'company_id' => session('company_id'),
        ]);
    }
}
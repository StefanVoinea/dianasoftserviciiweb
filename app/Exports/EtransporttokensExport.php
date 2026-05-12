<?php

namespace App\Exports;

use App\Models\Etransporttokens;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EtransporttokensExport implements FromQuery, WithHeadings
{
    use Exportable;

    protected $company_id;

    public function forCompany(int $company_id)
    {
        $this->company_id = $company_id;
        return $this;
    }

    public function headings(): array
    {
        return [
            "CUI",
            "Access Token",
            "Refresh Token",
            "Data Obținerii",
            "Data Expirare",
        ];
    }

    public function query()
    {
        return Etransporttokens::query()->select(
            "cui",
            "access_token",
            "refresh_token",
            "data_obtinerii",
            "data_expirare"
        )->where("company_id", $this->company_id);
    }
}
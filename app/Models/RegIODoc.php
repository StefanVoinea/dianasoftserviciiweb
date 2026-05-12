<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegIODoc extends Model
{
    use RecordsActivity;
    protected $table ="regiodoc";
    protected $fillable = ["company_id","nr_inregistrare","data_inregistrare","tip_document","nr_document","data_document","de_unde_provine","continut_pe_scurt","compartimentul_caruia_se_adreseaza","compartimentul_caruia_sa_repartizat","data_expedierii","destinatar","nr_inregistrare_existent"];
}
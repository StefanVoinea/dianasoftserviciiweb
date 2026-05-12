<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Efacturaparams extends Model
{
   // use RecordsActivity;
    protected $table ="efacturaparams";
    protected $fillable = ["link_authorization","link_token","link_revoke_token","link_upload","link_test_upload","link_stare_mesaj","link_test_stare_mesaj","link_lista_mesaje","link_test_lista_mesaje","link_lista_mesaje_cu_paginatie","link_test_lista_mesaje_cu_paginatie","link_descarcare_raspuns","link_test_descarcare_raspuns","link_validare_xml","link_transform_xml_to_pdf","linie_versiune_efactura",];
}
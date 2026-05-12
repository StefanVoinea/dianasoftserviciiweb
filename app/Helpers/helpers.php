<?php

use App\Models\Bank;
use App\Models\Datefinanciarepj;
use App\Models\Datefirmeregcom;
use App\Models\Facturiprimiteefactura;
use App\Models\Interogareanaf;
use App\Models\Litigiicaleatac;
use App\Models\Litigiiparti;
use App\Models\Litigiisedinte;
use App\Models\Litigiu;
use App\Models\Nombanci;
use App\Models\Notificationlog;
use App\Models\Notificationtype;
use App\Models\Partener;
use App\Models\RefreshToken;
use App\Models\Sarbatorilegale;
use App\Models\Tokenuri;
use App\Models\TranslateNumberToTxt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Mtownsend\XmlToArray\XmlToArray;
use VIPSoft\Unzip\Unzip;
use GuzzleHttp\Client;
use App\Mail\AlertaEroareEmail;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

function adaugaZileLucratoare($dataStart, $nrZile)
{
    $data = Carbon::parse($dataStart);
    $k = 0;

    while ($k < $nrZile) {
        $data->addDay();

        if (esteZiLucratoare($data)) {
            $k++;
        }
    }

    return $data->format("Y-m-d");
}

function parseRomanianDate(?string $s): ?string
{
    if (!$s) {
        return null;
    }
    $s = trim($s);

    $map = [
            'ian' => '01','feb' => '02','mar' => '03','apr' => '04','mai' => '05','iun' => '06',
            'iul' => '07','aug' => '08','sep' => '09','oct' => '10','nov' => '11','dec' => '12',
        ];

    if (!preg_match('/(\d{1,2})\s+([a-zăîâșț\.]{3,})\s+(\d{4})/iu', $s, $m)) {
        return null;
    }

    $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
    $monRaw = mb_strtolower(rtrim($m[2], '.'));
    $mon = $map[$monRaw] ?? null;
    if (!$mon) {
        return null;
    }

    return "{$m[3]}-{$mon}-{$day}";
}
function normalizeMoney($value): ?float
{
    if (!is_string($value) && !is_numeric($value)) {
        return null;
    }

    $value = trim((string)$value);

    // elimină spații
    $value = str_replace(' ', '', $value);

    // dacă are și . și , trebuie să decidem care e separatorul zecimal

    if (str_contains($value, '.') && str_contains($value, ',')) {
        if (strrpos($value, '.') > strrpos($value, ',')) {
            // format 12,123.50
            $value = str_replace(',', '', $value);
        } else {
            // format 12.123,50
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
    } else {
        // doar virgulă = separator zecimal
        if (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }
    }

    return is_numeric($value) ? (float)$value : null;
}
function isRealDate($value): bool
{
    if (!is_string($value) || trim($value) === '') {
        return false;
    }

    $value = trim($value);

    $formats = [
        'Y-m-d',
        'd.m.Y',
        'd/m/Y',
        'Y/m/d',
        'Ymd',
    ];

    foreach ($formats as $format) {
        $dt = DateTime::createFromFormat($format, $value);
        if ($dt && $dt->format($format) === $value) {
            return true;
        }
    }

    return false;
}
function normalizeDate($value): ?Carbon
{
    if ($value === null || $value === '') {
        return null;
    }

    // 1) Număr serial Excel
    if (is_numeric($value)) {
        // Dacă ai ore în Excel, păstrează-le cu ->excelToDateTimeObject

        return Carbon::instance(ExcelDate::excelToDateTimeObject($value));
    }

    // 2) Obiect DateTime deja
    if ($value instanceof \DateTimeInterface) {
        return Carbon::instance($value);
    }

    // 3) String — încearcă mai multe formate
    if (is_string($value)) {
        $formats = [
            'd.m.Y', 'd.m.y',
            'd/m/Y', 'd/m/y',
            'd-m-Y', 'd-m-y',
            'Y-m-d', 'Y/m/d',
            'm/d/Y', 'm-d-Y',
        ];

        foreach ($formats as $fmt) {
            $dt = Carbon::createFromFormat($fmt, trim($value));
            if ($dt !== false) {
                return $dt;
            }
        }

        // fallback: încearcă parserul liber al lui Carbon
        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            // dacă vrei, poți arunca sau loga și să marchezi rândul ca failed
                return null; // sau throw ValidationException::withMessages([...])
        }
    }

    return null;
}





function getWorkingDaysDiff(Carbon $startDate, Carbon $endDate)
{
    $workingDays = 0;
    // Clone the dates to avoid modifying the original ones
    $date = $startDate->copy();

    // Iterate through each day
    while ($date->lte($endDate)) {
        // If the day is not a weekend, count it as a working day
        $sarbatoare=Sarbatorilegale::where("data", $date)->get()->first();
        if (!$date->isWeekend()&&!$sarbatoare) {
            $workingDays++;
        }
        // Move to the next day
        $date->addDay();
    }
    return $workingDays;
}

function numarroman($numar)
{
    switch ($numar) {
      case 1: return "I";
      case 2: return "II";
      case 3: return "III";
      case 4: return "IV";
      case 5: return "V";
      case 6: return "VI";
      case 7: return "VII";
      case 8: return "VIII";
      case 9: return "IX";
      case 10: return "X";

  }
}


function sirinversat($sir)
{
    $sirinversat="";
    for ($i=0 ; $i<strlen($sir) ; $i++) {
        $sirinversat = $sirinversat . mid($sir, strlen($sir) + 1 - $i, 1);
    }
    return $sirinversat;
}
function anonimizarecifre($cod)
{
    $sir = sirinversat(codfaralitere($cod));
    $rez="";
    for ($i=0;$i<strlen($sir);$i++) {
        $rez = $rez . chr(ord(mid($sir, $i, 1)) + 20 + mid($sir, $i, 1));
    }
    return  $rez;
}

function replaceOutsideQuotes($search, $replace, $subject)
{
    // Define the regular expression pattern
    $pattern = '/(".*?"|\'.*?\'|[^"\']+)/';

    // Use preg_replace_callback to apply the replacement
    return preg_replace_callback($pattern, function ($matches) use ($search, $replace) {
        // Check if the match is a quoted string
        if ($matches[1][0] === '"' || $matches[1][0] === '\'') {
            // Return the quoted string as-is
            return $matches[1];
        } else {
            // Perform the replacement outside quotes
            return str_replace($search, $replace, $matches[1]);
        }
    }, $subject);
}

function virgulapret($cod)
{
    $codNou="";
    for ($i=0 ; $i<strlen($cod) ; $i++) {
        if (substr($cod, $i, 1)== ",") {
            $codNou = $codNou. "." ;
        } else {
            if (substr($cod, $i, 1) == ".") {
                $codNou = $codNou. "" ;
            } else {
                $codNou = $codNou. substr($cod, $i, 1) ;
            }
        }
    }
    return $codNou;
}

function downloadFile($url, $fileName)
{
    $filePath =  $fileName;
    $client = new Client();
    $response = $client->get($url);
    if ($response->getStatusCode() == 200) {
        // Salvează conținutul fișierului în sistemul de fișiere Laravel
        Storage::put($filePath, $response->getBody());
    }
}

function apeleazaAPI($linkAPI, $metoda, $tip_continut, $access_token, $xml)
{
    ob_end_clean();
    ob_start();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $linkAPI);
    $authorization="";
    $contentType="";
    if ($access_token!="") {
        $authorization = "Authorization: Bearer ".$access_token; // Prepare the authorisation token
    //curl_setopt($ch, CURLOPT_HTTPHEADER,  array($authorization)); // Inject the token into the header
    }

    if ($tip_continut!="") {
        $contentType="Content-Type: ".$tip_continut;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, array($contentType,$authorization)); // Inject the token into the header



    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($metoda!="") {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metoda);
    }
    if ($xml!="") {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    }
    $raspuns = curl_exec($ch);
    if (curl_errno($ch)) {
        // moving to display page to display curl errors


        $raspuns="EROARE ".curl_errno($ch);
    }


    curl_close($ch);

    ob_end_clean();
    ob_start();
    return $raspuns;
}

function XMLKeyValue($document, $tag)
{
    $array = XmlToArray::convert($document);
    if (array_key_exists($tag, $array)) {
        return $array[$tag];
    } else {
        return null;
    }
}
function XMLTagValue($document, $tag)
{
    $array = XmlToArray::convert($document);
    if (array_key_exists($tag, $array)) {
        return $array[$tag];
    } else {
        return null;
    }
}
function codfaralitere($cod)
{
    return preg_replace('/[^0-9]/', '', $cod);
}
function GetNewToken($company)
{
    $link="https://maya.bancomatic.ro/api/gettoken?cui=" . trim(codfaralitere($company->cui));
    $method="POST";
    $content_type=""; //"application/x-www-form-urlencoded; charset=utf-8";
    $access_token="";
    $raspuns=trim(apeleazaAPI($link, $method, $content_type, $access_token, ""));

    if (!str_contains($raspuns, "EROARE")) {
        $raspuns=json_decode($raspuns);
        // //Log::info($raspuns);
        Tokenuri::create([
        "access_token"=>$raspuns->access_token,
        "refresh_token"=>$raspuns->refresh_token,
        "data_expirarii"=>dateFormatStocare($raspuns->data_expirare),
        "data_obtinere"=>dateFormatStocare($raspuns->data_obtinerii),
    ]);
    }
}
function GetEfacturaParams($mediu)
{
    $linkAPI="https://maya.bancomatic.ro/api/efacturaparams";
    $metoda="GET";
    $tip_continut="text/plain";
    $access_token="";
    $raspuns=trim(apeleazaAPI($linkAPI, $metoda, $tip_continut, $access_token, ""));
    return json_decode($raspuns);
}

function verificareSPVFP($company)
{
    $efacturaParams=GetEfacturaParams("");
    $folderverificate = '/public/'.$company->slug."/efactura/fp/";
    $fisierRaport = $folderverificate . "raport_verificare_lista_mesaje" . "_" .time() . ".txt";
    ob_end_clean();
    ob_start();
    Storage::put($fisierRaport, "Raport verificare facturi primite  efectuata in " . Carbon::now());
    Storage::append($fisierRaport, "");
    $continut = "";
    $access_token = "";
    $tokenuri=DB::select(DB::raw("SELECT * FROM tokenuri
      WHERE tokenuri.data_expirarii>'".Carbon::today()->format("Y-m-d")."' AND tokenuri.data_obtinere<='".Carbon::today()->format("Y-m-d")."';"));



    if (count($tokenuri) > 0) {
        $access_token = $tokenuri[0]->access_token;
    }

    //DACA ESTE NULL ACCES TOKEN OBTIN TOKEN SAU REFRESH TOKEN
    if ($access_token== "") {
        GetNewToken($company);
        $tokenuri=DB::select(DB::raw("SELECT * FROM tokenuri
      WHERE tokenuri.data_expirarii>'".Carbon::today()->format("Y-m-d")."' AND tokenuri.data_obtinere<='".Carbon::today()->format("Y-m-d")."';"));


        if (count($tokenuri) > 0) {
            $access_token = $tokenuri[0]->access_token;
        }
    }

    $linkAPI=$efacturaParams->link_lista_mesaje."?zile=30&cif=" . codfaralitere($company->cui);
    $metoda="GET";
    $tip_continut=""; //"application/json";
    $raspuns=apeleazaAPI($linkAPI, $metoda, $tip_continut, $access_token, "");
    $response=$raspuns;
    //getting response from server
    //$raspuns = new \DOMDocument();
    //$raspuns->loadXML($response);
    if (array_key_exists('eroare', json_decode($response))) {
        Storage::append($fisierRaport, "EROARE :" . $response->eroare);
        //TREBUIE SA INCHEI SI SA RETURNEZ FISIERUL RAPORT
    }
    if ((array_key_exists('mesaje', json_decode($response)))) {
        foreach (json_decode($response)->mesaje as $mesaj) {
            if ($mesaj->tip=="FACTURA PRIMITA") {
                if (count(Facturiprimiteefactura::where("id_incarcare", $mesaj->id_solicitare)->get()) == 0) {
                    $data_mesaj = $mesaj->data_creare;
                    $data_mesaj = substr($data_mesaj, 0, 4). "/". substr($data_mesaj, 4, 2) . "/" .substr($data_mesaj, 6, 2)   .  " " . substr($data_mesaj, 8, 2) . ":" . substr($data_mesaj, 10, 2);
                    $id_descarcare = $mesaj->id;
                    $id_incarcare = $mesaj->id_solicitare;
                    Storage::append($fisierRaport, datasioraFormatAfisare($data_mesaj) . " ".$mesaj->tip. " ID Incarcare:".$mesaj->id_solicitare." ID Descarcare:".$mesaj->id." CIF Beneficiar:".$mesaj->cif);
                    //descarc factura
                    $linkAPI=$efacturaParams->link_descarcare_raspuns . $id_descarcare;
                    $metoda="GET";
                    $tip_continut="";//"application/x-www-form-urlencoded; charset=utf-8";
                    try {
                        $raspuns=apeleazaAPI($linkAPI, $metoda, $tip_continut, $access_token, "");
                        $numeZIP = $folderverificate . $id_descarcare . ".zip";
                        if (File::exists($numeZIP)) {
                            File::delete($numeZIP);
                        };
                        ob_end_clean();
                        ob_start();
                        $body = $raspuns;//->getBody();
                        // Explicitly cast the body to a string
                        $stringBody = (string) $body;
                        Storage::put($numeZIP, $stringBody);
                        //Madzipper::make($numeZIP)->extractTo($folderverificate);
                        $unzipper  = new Unzip();
                        $filenames = $unzipper->extract(storage_path("/app/public/".$company->slug."/efactura/fp/".$id_descarcare.".zip"), storage_path("/app/public/".$company->slug."/efactura/fp/"));
                        $continut = Storage::get($folderverificate . $id_incarcare . ".xml");
                        $continut=str_replace(" http://docs.oasis-open.org/ubl/os-UBL-2.1/xsd/maindoc/UBL-Invoice-2.1.xsd", "", $continut);
                        if (str_contains($continut, "ns0:Invoice")) {
                            $cbc = "ns1";
                            $cac = "ns2";
                            $cba = "";
                        } else {
                            $cbc = "cbc";
                            $cac = "cac";
                            $cba = "cba";
                        }
                        if (str_contains($continut, $cbc.":DueDate*")) {
                            $termen_plata = XMLTagValue($continut, $cbc.":DueDate");
                        } else {
                            $termen_plata = XMLTagValue($continut, $cbc.":IssueDate");
                        }
                        $dateFurnizor = XMLTagValue($continut, $cac.":AccountingSupplierParty");
                        $totalFactura = XMLTagValue($continut, $cac . ":LegalMonetaryTotal");

                        $cifemitent="";
                        if (array_key_exists($cac.":PartyTaxScheme", $dateFurnizor[$cac.":Party"])) {
                            if (array_key_exists($cbc.":CompanyID", $dateFurnizor[$cac.":Party"][$cac.":PartyTaxScheme"])) {
                                $cifemitent=$dateFurnizor[$cac.":Party"][$cac.":PartyTaxScheme"][$cbc.":CompanyID"];
                            } else {
                                $cifemitent=$dateFurnizor[$cac.":Party"][$cac.":PartyLegalEntity"][$cbc.":CompanyID"];
                            }
                        } else {
                            if (is_array($dateFurnizor[$cac.":Party"][$cac.":PartyLegalEntity"][$cbc.":CompanyID"])) {
                                if (array_key_exists("@content", $dateFurnizor[$cac.":Party"][$cac.":PartyLegalEntity"][$cbc.":CompanyID"])) {
                                    $cifemitent=$dateFurnizor[$cac.":Party"][$cac.":PartyLegalEntity"][$cbc.":CompanyID"]["@content"];
                                } else {
                                    $dateFurnizor[$cac.":Party"][$cac.":PartyLegalEntity"][$cbc.":CompanyID"];
                                }
                            } else {
                                $dateFurnizor[$cac.":Party"][$cac.":PartyLegalEntity"][$cbc.":CompanyID"];
                            }
                        }

                        $efacturafp=Facturiprimiteefactura::create([
  "company_id"=>1,
  "data_mesaj"=>$data_mesaj,
  "id_incarcare"=>$id_incarcare,
  "id_descarcare"=>$id_descarcare,
  "cif_beneficiar"=>$mesaj->cif,
  "tip_mesaj"=>$mesaj->tip,
  "detalii"=>$mesaj->detalii,
  "cif_emitent"=>$cifemitent,
  "data_descarcare"=>Carbon::now(),
  "nr_document" => XMLTagValue($continut, $cbc.":ID"),
  "data_document" => XMLTagValue($continut, $cbc.":IssueDate"),
  "termen_plata"=>$termen_plata,
  "partener" => $dateFurnizor[$cac.":Party"][$cac.":PartyLegalEntity"][$cbc.":RegistrationName"],
  "valoare_fara_tva" => $totalFactura[$cbc.":LineExtensionAmount"]["@content"],
  "valoare" => $totalFactura[$cbc.":TaxInclusiveAmount"]["@content"],
  "valoare_tva" =>$totalFactura[$cbc.":TaxInclusiveAmount"]["@content"]-$totalFactura[$cbc.":LineExtensionAmount"]["@content"],
  "status" => "In curs de verificare"
]);
                        Storage::append($fisierRaport, "                                DESCARCA ZIP CU SUCCES " . $efacturafp->nr_document. "/".dateFormatAfisare($efacturafp->data_document). " " .$efacturafp->partener);

                        //TRANSFORM XML IN PDF
                        $linkAPI=$efacturaParams->link_transform_xml_to_pdf;
                        $metoda="POST";
                        $tip_continut="text/plain";
                        $raspuns=apeleazaAPI($linkAPI, $metoda, $tip_continut, "", $continut);

                        $body = $raspuns;
                        // Explicitly cast the body to a string
                        $stringBody = (string) $body;
                        $numePDF = $folderverificate . $id_incarcare . ".pdf";
                        Storage::put($numePDF, $stringBody);
                        Storage::append($fisierRaport, "                                TRANSFORMARE CU SUCCES IN PDF " . $efacturafp->nr_document. "/".dateFormatAfisare($efacturafp->data_document). " " .$efacturafp->partener);
                    } catch (\Exception $e) {
                        Storage::append($fisierRaport, "EROARE : NU POT DESCARCA ID:".$id_descarcare);
                        Mail::to("stefan.voinea@gmail.com")
    ->send(new AlertaEroareEmail("VERIFICARE SPV NU POT DESCARCA ID ".$id_descarcare, $e->getMessage(), $e, Auth::user()));
                    }
                }
            }
        }
    }
    return  $fisierRaport ;
}
function right($sir, $nr)
{
    return substr($sir, strlen($sir)-$nr, $nr);
}
function left($sir, $nr)
{
    return substr($sir, 0, $nr);
}
function mid($sir, $poz, $nr)
{
    return substr($sir, $poz, $nr);
}
function tabSeparator()
{
    return "\t";
}
function campXML(string $den, string $val=null)
{
    if (strtoupper($val) == "LEI") {
        return " " . $den . "=\"RON\" ";
    }
    if ($val == "") {
        return  "";
    } else {
        return " " . $den . "=\"" . $val . "\" ";
    }
}
function preiaDosareInstanta($numarDosar=null, $obiectDosar=null, $numeParte=null, $institutie=null, $dataStart=null, $dataStop=null)
{
    $xml='<?xml version="1.0" encoding="utf-8"?>
 <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
 <CautareDosare xmlns="portalquery.just.ro">'.
 ($numarDosar ? '<numarDosar>'.$numarDosar.'</numarDosar>' : '').
 ($obiectDosar ? '<obiectDosar>'.$obiectDosar.'</obiectDosar>' : '').
 ($numeParte ? '<numeParte>'.$numeParte.'</numeParte>' : '').
 ($institutie ? '<institutie>'.$institutie.'</institutie>' : '').
 ($dataStart ? '<dataStart>'.$dataStart.'</dataStart>' : '').
 ($dataStop ? '<dataStop>'.$dataStop.'</dataStop>' : '').
 '</CautareDosare>
 </soap:Body>
 </soap:Envelope>';
    Log::info($xml);
    $litigii=[];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://portalquery.just.ro/Query.asmx?wsdl");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["content-type: text/xml; charset=utf-8"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    if (curl_errno($ch)) {
        // moving to display page to display curl errors
      //Log::info("EROARE LA PRELUARE DOSAR INSTANTA ".curl_errno($ch)) ;
      //Log::info("EROARE LA PRELUARE DOSAR INSTANTA ".curl_errno($ch));
    } else {
        //getting response from server
        $response = curl_exec($ch);
        Log::info($response);
        $clean_xml = str_ireplace('xmlns="portalquery.just.ro"', 'xmlns="http://portalquery.just.ro"', $response);
        Log::info($clean_xml);
        $clean_xml = str_ireplace(['<soap:Envelope',
   'xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"',
   'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"',
   'xmlns:xsd="http://www.w3.org/2001/XMLSchema">',
   '<soap:Body>',
   '</soap:Envelope>',
   '</soap:Body>',
   'xsi:nil="true"'], '', $clean_xml);



        $doc = new \DOMDocument();
        $doc->loadXML($clean_xml);
        $dosare = $doc->getElementsByTagName('Dosar');

        foreach ($dosare as $dosar) {
            $parti=[];
            $sedinte=[];
            $caideatac=[];

            $litigiu= new \Stdclass();
            $idDosar=Str::random(9);
            $litigiu->id=$idDosar;
            $litigiu->numar_dosar=valoareTagXMLDomDocument($dosar, 'numar');
            $litigiu->company_id=1;
            $litigiu->numar_dosar=valoareTagXMLDomDocument($dosar, 'numar');
            $litigiu->numar_vechi=valoareTagXMLDomDocument($dosar, 'numarVechi');
            $litigiu->data_dosar=left(valoareTagXMLDomDocument($dosar, 'data'), 10);

            $litigiu->institutie=valoareTagXMLDomDocument($dosar, 'institutie');
            $litigiu->departament=valoareTagXMLDomDocument($dosar, 'departament');
            $litigiu->categorie_caz=valoareTagXMLDomDocument($dosar, 'categorieCaz');
            $litigiu->stadiu_procesual=valoareTagXMLDomDocument($dosar, 'stadiuProcesual');
            $litigiu->obiect=valoareTagXMLDomDocument($dosar, 'obiect');
            $litigiu->data_modificare=valoareTagXMLDomDocument($dosar, 'dataModificare');
            $litigiu->categorie_caz_nume=valoareTagXMLDomDocument($dosar, 'categorieCazNume');
            $litigiu->stadiu_procesual_nume=valoareTagXMLDomDocument($dosar, 'stadiuProcesualNume');
            $litigiu->avocatul_apararii=$request->avocatul_apararii??null;
            $litigiu->avocatul_acuzarii=$request->avocatul_acuzarii??null;
            $litigiu->observatii=$request->observatii??null;
            $litigiu->email_alerte=$request->email_alerte??null;
            $litigiu->telefon_alerte=$request->telefon_alerte??null;
            $litigiu->status=$request->status??null;
            $litigiu->taxa_de_timbru=$request->taxa_de_timbru??null;
            $litigiu->cheltuieli_de_judecata=$request->cheltuieli_de_judecata??null;
            $litigiu->parti=null;
            $litigiu->litigiiparti=[];
            $litigiu->litigiisedinte=[];
            $litigiu->litigiicaleatac=[];

            //$litigiu=Litigiu::create($litigiu);
            $partiDosar=$dosar->getElementsByTagName('DosarParte');
            foreach ($partiDosar as $parte) {
                $parteLitigiu= new \StdClass();
                $parteLitigiu->company_id=1;
                $parteLitigiu->litigiu_id=$idDosar;
                $parteLitigiu->nume=valoareTagXMLDomDocument($parte, 'nume');
                $parteLitigiu->calitate=valoareTagXMLDomDocument($parte, 'calitateParte');
                //$parteLitigiu=Litigiiparti::create($parteLitigiu);
                //$litigiu->update(["parti"=>$litigiu->parti.valoareTagXMLDomDocument($parte,'nume').
                //                                    " in calitate de ".valoareTagXMLDomDocument($parte,'calitateParte').PHP_EOL]) ;

                $litigiu->parti=$litigiu->parti.valoareTagXMLDomDocument($parte, 'nume').' in calitate de '.valoareTagXMLDomDocument($parte, 'calitateParte').PHP_EOL ;
                array_push($parti, $parteLitigiu);
            }
            $litigiu->litigiiparti=$parti;
            $dosareSedinte=$dosar->getElementsByTagName('DosarSedinta');

            foreach ($dosareSedinte as $dosarSedinta) {
                $sedintaLitigiu=new \StdClass();
                $sedintaLitigiu->company_id=1;
                $sedintaLitigiu->litigiu_id=$idDosar;
                $sedintaLitigiu->complet=valoareTagXMLDomDocument($dosarSedinta, 'complet');
                $sedintaLitigiu->data_sedinta=dateFormatStocare(left(valoareTagXMLDomDocument($dosarSedinta, 'data'), 10));
                $sedintaLitigiu->ora_sedinta=valoareTagXMLDomDocument($dosarSedinta, 'ora');
                $sedintaLitigiu->solutie=valoareTagXMLDomDocument($dosarSedinta, 'solutie');
                $sedintaLitigiu->solutie_sumar=valoareTagXMLDomDocument($dosarSedinta, 'solutieSumar');
                $sedintaLitigiu->data_pronuntare=dateFormatStocare(left(valoareTagXMLDomDocument($dosarSedinta, "dataPronuntare"), 10));
                $sedintaLitigiu->document_sedinta=valoareTagXMLDomDocument($dosarSedinta, 'documentSedinta');
                $sedintaLitigiu->numar_document=valoareTagXMLDomDocument($dosarSedinta, 'numarDocument');
                $sedintaLitigiu->data_document=dateFormatStocare(left(valoareTagXMLDomDocument($dosarSedinta, "dataDocument"), 10));


                //$sedintaLitigiu=Litigiisedinte::create($sedintaLitigiu);
                array_push($sedinte, $sedintaLitigiu);
            }
            $litigiu->litigiisedinte=$sedinte;
            $caiAtac=$dosar->getElementsByTagName('DosarCaleAtac');



            foreach ($caiAtac as $caleAtac) {
                $caledeatac=new \StdClass();
                $caledeatac->company_id=1;
                $caledeatac->litigiu_id=$idDosar;
                $caledeatac->data_declarare=dateFormatStocare(left(valoareTagXMLDomDocument($caleAtac, 'dataDeclarare'), 10));
                $caledeatac->parte_declaratoare=valoareTagXMLDomDocument($caleAtac, 'parteDeclaratoare');
                $caledeatac->tip_cale_atac=valoareTagXMLDomDocument($caleAtac, 'tipCaleAtac');
                //$caledeatac=Litigiicaleatac::create($caledeatac);
                array_push($caideatac, $caledeatac);
            }

            $litigiu->litigiicaleatac=$caideatac;
            array_push($litigii, $litigiu);
        }
        curl_close($ch);
    }

    return $litigii;
}
function valoareTagXMLDomDocument($document, $tag)
{
    $valoare=trim($document->getElementsByTagName($tag)->item(0)->nodeValue);
    if (!$valoare) {
        $valoare=null;
    }
    return $valoare;
}

function primazitrimestrucurent($luna, $anul)
{
    switch ($luna) {

     case 1: return  Carbon::parse("01.01.".$anul);
     break;
     case 2: return  Carbon::parse("01.01.".$anul);
     break;
     case 3: return  Carbon::parse("01.01.".$anul);
     break;
     case 4: return  Carbon::parse("01.04.".$anul);
     break;
     case 5: return  Carbon::parse("01.04.".$anul);
     break;
     case 6: return  Carbon::parse("01.04.".$anul);
     break;
     case 7: return  Carbon::parse("01.07.".$anul);
     break;
     case 8: return  Carbon::parse("01.07.".$anul);
     break;
     case 9: return  Carbon::parse("01.07.".$anul);
     break;
     case 10: return  Carbon::parse("01.10.".$anul);
     break;
     case 11: return  Carbon::parse("01.10.".$anul);
     break;
     case 12: return  Carbon::parse("01.10.".$anul);
     break;

 }
}
function primazitrimestruanterior($luna, $anul)
{
    switch ($luna) {

     case 1:  return Carbon::parse("01.10." .($anul - 1));
     break;
     case 2:  return Carbon::parse("01.10." .($anul - 1));
     break;
     case 3:  return Carbon::parse("01.10." .($anul - 1));
     break;
     case 4:  return Carbon::parse("01.01." .$anul);
     break;
     case 5:  return Carbon::parse("01.01." .$anul);
     break;
     case 6:  return Carbon::parse("01.01." .$anul);
     break;
     case 7:  return Carbon::parse("01.04." .$anul);
     break;
     case 8:  return Carbon::parse("01.04." .$anul);
     break;
     case 9:  return Carbon::parse("01.04." .$anul);
     break;
     case 10: return Carbon::parse("01.07." .$anul);
     break;
     case 11: return Carbon::parse("01.07." .$anul);
     break;
     case 12: return Carbon::parse("01.07." .$anul);
     break;

 }
}

function ultimazitrimestrucurent($luna, $anul)
{
    switch ($luna) {

     case 1:  return Carbon::parse("03/01/".$anul)->endOfMonth();
     break;
     case 2:  return Carbon::parse("03/01/".$anul)->endOfMonth();
     break;
     case 3:  return Carbon::parse("03/01/".$anul)->endOfMonth();
     break;
     case 4:  return Carbon::parse("06/01/".$anul)->endOfMonth();
     break;
     case 5:  return Carbon::parse("06/01/".$anul)->endOfMonth();
     break;
     case 6:  return Carbon::parse("06/01/".$anul)->endOfMonth();
     break;
     case 7:  return Carbon::parse("09/01/".$anul)->endOfMonth();
     break;
     case 8:  return Carbon::parse("09/01/".$anul)->endOfMonth();
     break;
     case 9:  return Carbon::parse("09/01/".$anul)->endOfMonth();
     break;
     case 10: return Carbon::parse("12/01/".$anul)->endOfMonth();
     break;
     case 11: return Carbon::parse("12/01/".$anul)->endOfMonth();
     break;
     case 12: return Carbon::parse("12/01/".$anul)->endOfMonth();
     break;
 }
}

function ultimazitrimestruanterior($luna, $anul)
{
    switch ($luna) {

     case 1:  return Carbon::parse("12/01/".($anul - 1))->endOfMonth();
     break;
     case 2:  return Carbon::parse("12/01/".($anul - 1))->endOfMonth();
     break;
     case 3:  return Carbon::parse("12/01/".($anul - 1))->endOfMonth();
     break;
     case 4:  return Carbon::parse("03/01/".$anul)->endOfMonth();
     break;
     case 5:  return Carbon::parse("03/01/".$anul)->endOfMonth();
     break;
     case 6:  return Carbon::parse("03/01/".$anul)->endOfMonth();
     break;
     case 7:  return Carbon::parse("06/01/".$anul)->endOfMonth();
     break;
     case 8:  return Carbon::parse("06/01/".$anul)->endOfMonth();
     break;
     case 9:  return Carbon::parse("06/01/".$anul)->endOfMonth();
     break;
     case 10: return Carbon::parse("09/01/".$anul)->endOfMonth();
     break;
     case 11: return Carbon::parse("09/01/".$anul)->endOfMonth();
     break;
     case 12: return Carbon::parse("09/01/".$anul)->endOfMonth();
     break;

 }
}

function trimestruanterior($luna, $anul)
{
    switch ($luna) {

    case 1: return "IV-" .($anul - 1);
    break;
    case 2: return "IV-" .($anul - 1);
    break;
    case 3: return "IV-" .($anul - 1);
    break;
    case 4: return "I-" .$anul;
    break;
    case 5: return "I-" .$anul;
    break;
    case 6: return "I-" .$anul;
    break;
    case 7: return "II-" .$anul;
    break;
    case 8: return "II-" .$anul;
    break;
    case 9: return "II-" .$anul;
    break;
    case 10: return "III-" .$anul;
    break;
    case 11: return "III-" .$anul;
    break;
    case 12: return "III-" .$anul;
    break;


}
}

function trimestrucurent($luna, $anul)
{
    switch ($luna) {

    case 1: return "I-" .$anul;
    break;
    case 2: return "I-" .$anul;
    break;
    case 3: return "I-" .$anul;
    break;
    case 4: return "II-" .$anul;
    break;
    case 5: return "II-" .$anul;
    break;
    case 6: return "II-" .$anul;
    break;
    case 7: return "III-" .$anul;
    break;
    case 8: return "III-" .$anul;
    break;
    case 9: return "III-" .$anul;
    break;
    case 10: return "IV-" .$anul;
    break;
    case 11: return "IV-" .$anul;
    break;
    case 12: return "IV-" .$anul;
    break;
}
}



function numar_formatat($nr, $nr_zecimale)
{
    // try{
    //Log::info($nr);
    $nr=str_replace(" ", "", $nr);
    $nr=str_replace("eur", "", $nr);
    $nr=str_replace("lei", "", $nr);
    $nr=str_replace("EUR", "", $nr);
    $nr=str_replace("LEI", "", $nr);
    $nr=str_replace("ron", "", $nr);
    $nr=str_replace("RON", "", $nr);
    //Log::info($nr);
    $nr= (float) $nr;
    //Log::info($nr);
    return number_format($nr, $nr_zecimale);
    // }catch(Exception $e){

    //     return $nr;
    // }
}
function connectToAnafWS()
{
    $client = new \GuzzleHttp\Client(['headers' =>
    [
        'Accept'=> '*/*',
        'Content-Type' => 'application/xml',
    ],

    'cert' => [base_path().'/certificates/certificat_anaf.pem','Bradului13!'],
    'cainfo' => [base_path().'/certificates/rootca_anaf.pem','Bradului13!'],
    'ssl_key' => [base_path().'/certificates/private_anaf.key','Bradului13!'],

                                                  // 'debug' => true,
]);

    //   try{
    //Log::info(base_path());
    //Log::info("INCEP");
    $req = $client->get('https://financiar.anaf.ro/f5-w-68747470733a2f2f65787472616e65742e666973636e65742e726f$$/ContRes/listaMesaje', [ 'xml'=>'<header xmlns="mfp:anaf:dgti:banci:reqListaMesaje:v1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
       <listaMesaje Zile="3"/>
       </header>']);


    //Log::info("Termin");

    // } catch (\GuzzleHttp\Exception\BadResponseException $e) {
    //    //Log::info("EROARE");
    //    //Log::info($e->getCode());
    //    if($e->getCode()==400) {
    //        //Log::info( response()->json('Invalid Request, Please enter a username or a password.',$e->getCode()));
    //    } else if ($e->getCode()==401) {
    //        //Log::info(response()->json('Your credentials are incorrect. Please try again',$e->getCode()));
    //    }else{

    //    }

    //      return response()->json('Something went wrong on the server.', $e->getCode() );
    //  };
    $resp = (string) $req->getBody();
}
function transmiteNotificare($denumireNotificare, $notificare)
{
    $tipNotificare=Notificationtype::where("denumire", $denumireNotificare)->with(["notificationuser"])->get()->first();
    $users=User::whereIn("id", $tipNotificare->notificationuser->where("channel", "Email")->pluck("user_id"))->get();
    foreach ($users as $user) {
        Notification::send($user, $notificare);
    }
}
function puneInCalendar($start, $end, $titlu, $url, $allday, $location, $guests, $description, $participating_users)
{
    $guests=[strval(session("user_id"))];

    foreach ($request->extendedProps["guests"] as $guest) {
        $userGuest=User::where("id", $guest)->get()->first();

        if ($userGuest->grup) {
            $guests=array_merge($guests, $userGuest->grup);
        } else {
            array_push($guests, strval($guest["id"]));
        }
    }
    $description= Calendarevent::create([
  "company_id"=>session("company_id")??1,
  "createdby_id"=>session("user_id")??1,
  "title"=>$title,
  "url"=>$url,
  "start"=>datasioraFormatStocare($start),
  "end"=>datasioraFormatStocare($end),
  "allday"=>$allday??false,
  "calendar"=>$calendar,
  "guests"=>$guests,
  "location"=>$location,
  "description"=>$description,
  "participating_users"=>$guests,
]);
}

function notificare(String $tip_notificare, Object $object, $from_id, $email, $telefon, $mesaj, $link, $gestiune)
{
    $tipNotificare=Notificationtype::where("company_id", 1)
   ->where("denumire", $tip_notificare)
   ->with(["notificationuser"])
   ->get()->first();
    $usersTransmit=[];
    foreach ($tipNotificare->notificationuser as $userNotificat) {
        $notificationIcon="InfoIcon";
        switch ($tipNotificare->categoria) {
        case "info":
        $notificationIcon="InfoIcon";
        break;
        case "success":
        $notificationIcon="CheckIcon";
        break;
        case "danger":
        $notificationIcon="AlertOctagonIcon";
        break;

        case "warning":
        $notificationIcon="AlertTriangleIcon";
        break;
    }

        $userID=$userNotificat->user_id;
        if ($userNotificat->user["grup"]) {
            foreach ($userNotificat->user["grup"] as $userID) {
                if (!in_array($userID, $usersTransmit)) {
                    $user=User::where("id", $userID)->get()->first();
                    if ($user) {
                        $gestiuniPermise=$user->gestiuniPermiseCompany()->pluck("denumire");
                        if (in_array($gestiune, $gestiuniPermise->toArray())||$gestiune=="") {
                            Notificationlog::create([
                "subject_type"=>$object ? get_class($object) : null,
                "subject_id"=>$object ? $object->id : null,
                "company_id"=>1,
                "notificationtype_id"=>$tipNotificare->id,
                "from_id"=>$from_id,
                "user_id"=>$userID,
                "channel"=>$userNotificat->channel,
                "email"=>"",
                "telefon"=>"",
                "title"=>$tip_notificare,
                "subtitle"=>$mesaj,
                "type"=>"light-".$tipNotificare->categoria,
                "icon"=>$notificationIcon,
                "avatar"=>"",
                "link"=>$link ? env("APP_URL")."/".$link : "",
                "category"=>$tipNotificare->categoria,
            ]);
                            array_push($usersTransmit, $userID);
                        }
                    }
                }
            }
        } else {
            if (!in_array($userID, $usersTransmit)) {
                $user=User::where("id", $userID)->get()->first();
                if ($user) {
                    $gestiuniPermise=$user->gestiuniPermiseCompany()->pluck("denumire");

                    if (in_array($gestiune, $gestiuniPermise->toArray())||$gestiune=="") {
                        Notificationlog::create([
              "subject_type"=>$object ? get_class($object) : null,
              "subject_id"=>$object ? $object->id : null,
              "company_id"=>1,
              "notificationtype_id"=>$tipNotificare->id,
              "from_id"=>$from_id,
              "user_id"=>$userID,
              "channel"=>$userNotificat->channel,
              "email"=>"",
              "telefon"=>"",
              "title"=>$tip_notificare,
              "subtitle"=>$mesaj,
              "type"=>"light-".$tipNotificare->categoria,
              "icon"=>$notificationIcon,
              "avatar"=>"",
              "link"=>$link ? env("APP_URL")."/".$link : "",
              "category"=>$tipNotificare->categoria,
          ]);
                        array_push($usersTransmit, $userID);
                    }
                }
            }
        }
    }


    function getAllRelations(\Illuminate\Database\Eloquent\Model $model = null, $heritage = 'all')
    {
        $model = $model ?: $this;
        $modelName = get_class($model);
        $types = ['children' => 'Has', 'parents' => 'Belongs', 'all' => ''];
        $heritage = in_array($heritage, array_keys($types)) ? $heritage : 'all';
        if (\Illuminate\Support\Facades\Cache::has($modelName."_{$heritage}_relations")) {
            return \Illuminate\Support\Facades\Cache::get($modelName."_{$heritage}_relations");
        }

        $reflectionClass = new \ReflectionClass($model);
        $traits = $reflectionClass->getTraits();    // Use this to omit trait methods
        $traitMethodNames = [];
        foreach ($traits as $name => $trait) {
            $traitMethods = $trait->getMethods();
            foreach ($traitMethods as $traitMethod) {
                $traitMethodNames[] = $traitMethod->getName();
            }
        }

        // Checking the return value actually requires executing the method.  So use this to avoid infinite recursion.
        $currentMethod = collect(explode('::', __METHOD__))->last();
        $filter = $types[$heritage];
        $methods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);  // The method must be public

        $methods = collect($methods)->filter(function ($method) use ($modelName, $traitMethodNames, $currentMethod) {
            $methodName = $method->getName();

            if (!in_array($methodName, $traitMethodNames)   //The method must not originate in a trait
            && strpos($methodName, '__') !== 0  //It must not be a magic method
            && $method->class === $modelName    //It must be in the self scope and not inherited
            && !$method->isStatic() //It must be in the this scope and not static
            && $methodName != $currentMethod    //It must not be an override of this one
        ) {
                $parameters = (new \ReflectionMethod($modelName, $methodName))->getParameters();
                return collect($parameters)->filter(function ($parameter) {
                    return !$parameter->isOptional();   // The method must have no required parameters
                })->isEmpty();  // If required parameters exist, this will be false and omit this method
            }
            return false;
        })->mapWithKeys(function ($method) use ($model, $filter) {
            $methodName = $method->getName();

            $relation = $model->$methodName();  //Must return a Relation child. This is why we only want to do this once


            if (is_subclass_of($relation, \Illuminate\Database\Eloquent\Relations\Relation::class)) {
                $type = (new \ReflectionClass($relation))->getShortName();  //If relation is of the desired heritage
                if (!$filter || strpos($type, $filter) === 0) {
                    return [$methodName => get_class($relation->getRelated())]; // ['relationName'=>'relatedModelClass']
                }
            }
            return false;   // Remove elements reflecting methods that do not have the desired return type
        })->toArray();
        $allRelations=[];
        foreach ($methods as $key=>$relatie) {
            array_push($allRelations, $key);
        }
        \Illuminate\Support\Facades\Cache::forever($modelName."_{$heritage}_relations", $allRelations);
        return $allRelations;
    }



    function esteZiLucratoare($data)
    {
        if ($data->isWeekend()) {
            return false;
        }
        $sarbatoareLegala=Sarbatorilegale::where("data", Carbon::parse($data))->get()->first();
        if ($sarbatoareLegala) {
            return false;
        }
        return true;
    }
    function zilucratoare($dataScadenta)
    {
        if ($dataScadenta->isWeekend()) {
            return zilucratoare($dataScadenta->copy()->addDays(-1));
        }
        $sarbatoareLegala=Sarbatorilegale::where("data", Carbon::parse($dataScadenta))
->get()->first();
        if ($sarbatoareLegala) {
            return zilucratoare($dataScadenta->copy()->addDays(-1));
        }
        return $dataScadenta;
    }

    function datavalida($zi, $luna, $an)
    {
        while ($zi > 0) {
            $data = Carbon::createFromFormat(
                'd.m.Y',
                sprintf('%02d.%02d.%04d', $zi, $luna, $an)
            );

            // verificare strictă
            if ($data && $data->format('d.m.Y') === sprintf('%02d.%02d.%04d', $zi, $luna, $an)) {
                return $data;
            }

            $zi--;
        }

        return null;
    }


    function nz($valoare, $zero)
    {
        if ($valoare==null||$valoare==""||$valoare==0) {
            return $zero;
        } else {
            return $valoare;
        }
    }
    function valoareCamp($key, $arr)
    {
        try {
            if ($arr==null) {
                return null;
            }

            if (array_key_exists($key, $arr)) {
                return $arr[$key];
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    function lunainlitere($luna)
    {
        if ($luna==1) {
            return "Ianuarie";
        };
        if ($luna==2) {
            return "Februarie";
        };
        if ($luna==3) {
            return "Martie";
        };
        if ($luna==4) {
            return "Aprilie";
        };
        if ($luna==5) {
            return "Mai";
        };
        if ($luna==6) {
            return "Iunie";
        };
        if ($luna==7) {
            return "Iulie";
        };
        if ($luna==8) {
            return "August";
        };
        if ($luna==9) {
            return "Septembrie";
        };
        if ($luna==10) {
            return "Octombrie";
        };
        if ($luna==11) {
            return "Noiembrie";
        };
        if ($luna==12) {
            return "Decembrie";
        };
    }

    function sirintre($sir, $sirinceput, $sirsfarsit)
    {
        if ($sirinceput=="") {
            return Str::before($sir, $sirsfarsit);
        }
        if ($sirsfarsit=="") {
            return Str::after($sir, $sirinceput);
        }
        return Str::before(Str::after($sir, $sirinceput), $sirsfarsit);
    }
    function cursBNR($data, $valuta)
    {
        if (Str::upper($valuta)=="RON" || Str::upper($valuta)=="LEI") {
            return 1;
        }
        $curs= new \App\Models\Cursbnr();
        $datamax=\App\Models\Cursbnr::where('tip_valuta', $valuta)->where('data', '<=', $data)->max('data');
        $curs = \App\Models\Cursbnr::where('tip_valuta', $valuta)->where('data', $datamax)->first();
        //   $curs = \App\Models\Cursbnr::where('tip_valuta', $valuta)->where('data', dateformatStocare($data))->first();
        if (is_null($curs)) {
            return 0;
        } else {
            return $curs->curs;
        };
    }

    function dateFormatStocare($data)
    {
        if (!$data==null) {
            //$data=normalizeDate($data);
            return Carbon::parse($data)->format('Y-m-d') ;
        } else {
            return null;
        }
    }
    function datasioraFormatStocare($data)
    {
        if (!$data==null) {
            return Carbon::parse($data)->format('Y-m-d h:i:s') ;
        } else {
            return null;
        }
    }
    function dateFormatAfisare($data)
    {
        if (!$data==null) {
            return Carbon::parse($data)->format('d.m.Y');
        } else {
            return null;
        }
    }
    function datasioracurenta()
    {
        return Carbon::now()->format('d.m.Y H:i');
    }
    function datasioraFormatAfisare($data)
    {
        if (!$data==null) {
            return Carbon::parse($data)->format('d.m.Y H:i');
        } else {
            return null;
        }
    }
    function verificaCuiAnafBulk($cuiArray, $data)
    {
        $existaCUI=true;
        $currentDate = $data->format('Y-m-d');

        $sirCuiuri="[";
        $i=0;
        foreach ($cuiArray as $cui) {
            $cuifaralitere=preg_replace('/[^0-9]/', '', $cui);
            $sirCuiuri=$sirCuiuri."
        {\"cui\":".$cuifaralitere.", \"data\":\"".$currentDate."\"}";
            $i++;
            if ($i!=count($cuiArray)) {
                $sirCuiuri=$sirCuiuri.",";
            }
        };
        $sirCuiuri=$sirCuiuri."]";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["content-type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sirCuiuri);
        $resp = curl_exec($ch);
        curl_close($ch);
        // Interogareanaf::create([
        //            "company_id"=>session("company_id"),
        //            "user_id"=>session("user_id"),
        //            "cui"=>$cui,
        //            "data"=>Carbon::now(),
        //            "raspuns"=>$resp

        // ]);

        return $resp;
    }
    function bancaIBAN($iban)
    {
        $denBanca="";
        if ($iban!="") {
            $denBanca=Nombanci::where("cod", 'like', '%'.substr($iban, 4, 4).'%')
        ->get()->first()->denumire;
        }

        return $denBanca;
    }
    function verificaCuiAnaf($cui, $data)
    {
        Log::info("Verifica CUI PAS 1");
        $exista=Partener::where("cui", $cui)->get()->first();
        Log::info("Verifica CUI PAS 2");
        if ($exista) {
            $existaCUI=true;
        } else {
            $existaCUI=false;
        }
        $cuifaralitere=preg_replace('/[^0-9]/', '', $cui);
        $currentDate = $data->format('Y-m-d');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["content-type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "[
{
    \"cui\":".$cuifaralitere.", \"data\":\"".$currentDate."\"     
}       
]");
        $resp = curl_exec($ch);
        curl_close($ch);
        Log::info("Verifica CUI PAS 3");
        Interogareanaf::create([
    "company_id"=>session("company_id"),
    "user_id"=>session("user_id"),
    "cui"=>$cuifaralitere,
    "data"=>Carbon::now(),
    "raspuns"=>$resp

]);
        Log::info("Verifica CUI PAS 4");
        $firmaRegCom=Datefirmeregcom::where("cui", $cuifaralitere)->get()->first();
        Log::info("Verifica CUI PAS 5");
        $cont=json_decode($resp)->found[0]->date_generale->iban;
        Log::info("Verifica CUI PAS 6");


        $resptmp=json_encode(array_merge(json_decode($resp, true), ["bancaANAF"=>bancaIBAN($cont)]));
        if ($firmaRegCom) {
            $temp=$firmaRegCom->toJson();
            $response=json_encode(array_merge(json_decode($resptmp, true), json_decode($temp, true), ["exista"=>$existaCUI]));
        } else {
            $response=$resptmp;
        }
        Log::info("Verifica CUI PAS 7");
        //DATE FINANCIARE BILANT
        // try{
        $ch = curl_init();
        $anul=Carbon::today()->year-1;
        curl_setopt($ch, CURLOPT_URL, "https://webservicesp.anaf.ro/bilant?an=".$anul."&cui=".$cuifaralitere);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["content-type: application/json"]);
        $resp = curl_exec($ch);
        curl_close($ch);
        Log::info("Verifica CUI PAS 8");
        Log::info($anul);
        Log::info(json_decode($resp, true));
        if (count(json_decode($resp, true)["i"])==0) {
            $anul=$anul-1;
        } else {
        }
        if (count(Datefinanciarepj::where("cui", $cuifaralitere)->where("an", $anul)->get())==0) {
            foreach (json_decode($resp, true)["i"] as $linie) {
                Datefinanciarepj::create([
        "company_id"=>1,
        "cui"=>$cuifaralitere,
        "an"=>$anul,
        "indicator"=>$linie["indicator"],
        "val_indicator"=>trim($linie["val_indicator"]),
        "val_den_indicator"=>trim($linie["val_den_indicator"])
    ]);
            }
        }
        $anul=$anul-1;
        Log::info("Verifica CUI PAS 9");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://webservicesp.anaf.ro/bilant?an=".$anul."&cui=".$cuifaralitere);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["content-type: application/json"]);
        $resp = curl_exec($ch);
        curl_close($ch);
        Log::info("Verifica CUI PAS 10");
        //    try{
        Log::info($anul);
        Log::info(json_decode($resp, true));
        if (count(json_decode($resp, true)["i"])==0) {
            $anul=$anul-1;
        } else {
        }

        if (count(Datefinanciarepj::where("cui", $cuifaralitere)->where("an", $anul)->get())==0) {
            foreach (json_decode($resp, true)["i"] as $linie) {
                Datefinanciarepj::create([
        "company_id"=>1,
        "cui"=>$cuifaralitere,
        "an"=>$anul,
        "indicator"=>$linie["indicator"],
        "val_indicator"=>$linie["val_indicator"],
        "val_den_indicator"=>$linie["val_den_indicator"]
    ]);
            }
        }

        Log::info("Verifica CUI PAS 11");
        return $response;
    }

    function verificaCuiAnafFaraDateFinanciare($cui, $data)
    {
        Log::info("Verifica CUI PAS 1");
        $exista=Partener::where("cui", $cui)->get()->first();
        Log::info("Verifica CUI PAS 2");
        if ($exista) {
            $existaCUI=true;
        } else {
            $existaCUI=false;
        }
        $cuifaralitere=preg_replace('/[^0-9]/', '', $cui);
        $currentDate = $data->format('Y-m-d');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["content-type: application/json"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "[
{
    \"cui\":".$cuifaralitere.", \"data\":\"".$currentDate."\"     
}       
]");
        $resp = curl_exec($ch);
        curl_close($ch);
        Log::info("Verifica CUI PAS 3");
        Interogareanaf::create([
    "company_id"=>session("company_id"),
    "user_id"=>session("user_id"),
    "cui"=>$cuifaralitere,
    "data"=>Carbon::now(),
    "raspuns"=>$resp

]);
        Log::info("Verifica CUI PAS 4");
        $firmaRegCom=Datefirmeregcom::where("cui", $cuifaralitere)->get()->first();
        Log::info("Verifica CUI PAS 5");
        if (count(json_decode($resp)->found)) {
            $cont=json_decode($resp)->found[0]->date_generale->iban;
        } else {
            $cont="";
        }
        Log::info("Verifica CUI PAS 6");


        $resptmp=json_encode(array_merge(json_decode($resp, true), ["bancaANAF"=>bancaIBAN($cont)]));
        if ($firmaRegCom) {
            $temp=$firmaRegCom->toJson();
            $response=json_encode(array_merge(json_decode($resptmp, true), json_decode($temp, true), ["exista"=>$existaCUI]));
        } else {
            $response=$resptmp;
        }
        Log::info("Verifica CUI PAS 7");
        //DATE FINANCIARE BILANT
        // // try{
        //  $ch = curl_init();
        //  $anul=Carbon::today()->year-1;
        //  curl_setopt($ch, CURLOPT_URL, "https://webservicesp.anaf.ro/bilant?an=".$anul."&cui=".$cuifaralitere);
        //  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //  curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"GET" );
        //  curl_setopt($ch, CURLOPT_HTTPHEADER, ["content-type: application/json"]);
        //  $resp = curl_exec($ch);
        //  curl_close($ch);
        //  Log::info("Verifica CUI PAS 8");
        //  Log::info($anul);
        //  Log::info(json_decode($resp, true));
        //    if(count(json_decode($resp, true)["i"])==0){
        //     $anul=$anul-1;
        //    }else{




        //     }
        //       if(count(Datefinanciarepj::where("cui",$cuifaralitere)->where("an",$anul)->get())==0){
        //          foreach(json_decode($resp, true)["i"] as $linie){
        //                    Datefinanciarepj::create([
        //                        "company_id"=>1,
        //                        "cui"=>$cuifaralitere,
        //                        "an"=>$anul,
        //                        "indicator"=>$linie["indicator"],
        //                        "val_indicator"=>trim($linie["val_indicator"]),
        //                        "val_den_indicator"=>trim($linie["val_den_indicator"])
        //                    ]);
        //                }
        //       }
        //      $anul=$anul-1;
        //      Log::info("Verifica CUI PAS 9");
        //      $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, "https://webservicesp.anaf.ro/bilant?an=".$anul."&cui=".$cuifaralitere);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"GET" );
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, ["content-type: application/json"]);
        //     $resp = curl_exec($ch);
        //     curl_close($ch);
        //     Log::info("Verifica CUI PAS 10");
        // //    try{
        //     Log::info($anul);
        //     Log::info(json_decode($resp, true));
        //     if(count(json_decode($resp, true)["i"])==0){
        //        $anul=$anul-1;
        //     }else{


        //       }

        //  if(count(Datefinanciarepj::where("cui",$cuifaralitere)->where("an",$anul)->get())==0){
        // foreach(json_decode($resp, true)["i"] as $linie){
        //           Datefinanciarepj::create([
        //               "company_id"=>1,
        //               "cui"=>$cuifaralitere,
        //               "an"=>$anul,
        //               "indicator"=>$linie["indicator"],
        //               "val_indicator"=>$linie["val_indicator"],
        //               "val_den_indicator"=>$linie["val_den_indicator"]
        //           ]);
        //       }
        //   }

        Log::info("Verifica CUI PAS 11");
        return $response;
    }
    function validCUI($cui)
    {
        $cui = trim($cui);
        if (Str::startsWith($cui, "0")) {
            return false;
        }
        if (ctype_digit($cui) && strlen($cui) <= 10) {
            $key = '753217532';
            $key_rev = strrev($key);
            $cui_m1 = substr($cui, 0, -1);
            $cui_rev = strrev($cui_m1);
            $cui_len = strlen($cui_rev);
            $produse=0;
            for ($i = 0; $i <= ($cui_len - 1); $i++) {
                $produse += $cui_rev[$i] * $key_rev[$i];
            }
            $cifra_ver = ($produse*10)%11;
            if ($cifra_ver == 10) {
                $cifra_ver = 0;
            }

            if ($cifra_ver == $cui[$cui_len]) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    function validCNP($p_cnp)
    {
        // CNP must have 13 characters
        if (strlen($p_cnp) != 13) {
            return false;
        }
        $cnp = str_split($p_cnp);
        unset($p_cnp);
        $hashTable = array( 2 , 7 , 9 , 1 , 4 , 6 , 3 , 5 , 8 , 2 , 7 , 9 );
        $hashResult = 0;
        // All characters must be numeric
        for ($i=0 ; $i<13 ; $i++) {
            if (!is_numeric($cnp[$i])) {
                return false;
            }
            $cnp[$i] = (int)$cnp[$i];
            if ($i < 12) {
                $hashResult += (int)$cnp[$i] * (int)$hashTable[$i];
            }
        }
        unset($hashTable, $i);
        $hashResult = $hashResult % 11;
        if ($hashResult == 10) {
            $hashResult = 1;
        }
        // Check Year
        $year = ($cnp[1] * 10) + $cnp[2];
        switch ($cnp[0]) {
        case 1: case 2: { $year += 1900; } break; // cetateni romani nascuti intre 1 ian 1900 si 31 dec 1999
        case 3: case 4: { $year += 1800; } break; // cetateni romani nascuti intre 1 ian 1800 si 31 dec 1899
        case 5: case 6: { $year += 2000; } break; // cetateni romani nascuti intre 1 ian 2000 si 31 dec 2099
        case 7: case 8: case 9: {                // rezidenti si Cetateni Straini
            $year += 2000;
            if ($year > (int)date('Y')-14) {
                $year -= 100;
            }
        } break;
        default: {
            return false;
        } break;
    }
        return ($year > 1800 && $year < 2099 && $cnp[12] == $hashResult);
    }
    function extractJudet($adresaCompleta)
    {
        $judet="";
        $adresaArray= explode(",", $adresaCompleta);
        foreach ($adresaArray as $adresa) {
            if (Str::contains($adresa, "Județ")) {
                $judet=trim(str_replace("Județ", "", $adresa));
            }
            if (Str::startsWith($adresa, "Bucureşti")) {
                $judet="Bucureşti";
            }
        };
        return $judet;
    }
    function extractLocalitate($adresaCompleta)
    {
        $localitate="";
        $adresaArray= explode(",", $adresaCompleta);
        foreach ($adresaArray as $adresa) {
            if (Str::startsWith($adresa, "Bucureşti")) {
                $localitate=$adresa;
            }
            if (Str::contains($adresa, "Municipiul")) {
                $localitate=$localitate ." ".trim(str_replace("Municipiul", "", $adresa));
            }
            if (Str::contains($adresa, "Oraş")) {
                $localitate=trim(str_replace("Oraş", "", $adresa));
            }
            if (Str::contains($adresa, "Comuna")) {
                $localitate=$localitate." ".trim(str_replace("Comuna", "", $adresa));
            }
            if (Str::startsWith($adresa, "Sat ")) {
                $localitate= $adresa;
            }
            if (Str::contains($adresa, "Loc.")) {
                $localitate=$localitate." ".trim(str_replace("Loc.", "", $adresa));
            }
        };
        return $localitate;
    }

    function creezFiltru($key, $value, $result)
    {
        switch ($value["type"]) {
       case "inRange":
       if (Str::contains($key, ".")) {
           $caut=explode(".", $key);
           $result->whereHas(
               $caut[0],
               function ($q) use ($caut, $value) {
                if ($value["filterType"]=="date") {
                    $q->where($caut[1], '>=', escape_like($value['dateFrom']))
                 ->where($caut[1], '<=', escape_like($value['dateTo']));
                } else {
                    $q->where($caut[1], '>=', escape_like($value['filter']))
              ->where($caut[1], '<=', escape_like($value['filterTo']));
                }
            }
           );
       } else {
         if ($value["filterType"]=="date") {
             $result->where($key, '>=', escape_like($value["dateFrom"]))
        ->where($key, '<=', escape_like($value["dateTo"])) ;
         } else {
             $result->where($key, '>=', escape_like($value["filter"]))
        ->where($key, '<=', escape_like($value["filterTo"])) ;
         }
     }

break;
case "contains":
if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        $q->where($caut[1], 'like', '%'.escape_like($value['filter']).'%');
    }
    );
} else {
    $result->where($key, 'like', '%'.escape_like($value["filter"]).'%');
}

break;
case "notContains":
if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        $q->where($caut[1], 'not like', '%'.escape_like($value['filter']).'%');
    }
    );
} else {
    $result->where($key, 'not like', '%'.escape_like($value["filter"]).'%');
}
break;

case "equals":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, escape_like($value["dateFrom"]));
    } else {
        $result->where($key, escape_like($value["filter"]));
    }
}

break;
case "greaterThan":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '>', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '>', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '>', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '>', escape_like($value["filter"]));
    }
}

break;
case "greaterThanOrEqual":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '>=', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '>=', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '>=', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '>=', escape_like($value["filter"]));
    }
}

break;
case "lessThan":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '<', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '<', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '<', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '<', escape_like($value["filter"]));
    }
}

break;
case "lessThanOrEqual":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '<=', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '<=', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '<=', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '<=', escape_like($value["filter"]));
    }
}

break;
case "notEqual":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '<>', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '<>', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '<>', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '<>', escape_like($value["filter"]));
    }
}

break;
case "startsWith":
if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        $q->where($caut[1], 'like', escape_like($value['filter']).'%');
    }
    );
} else {
    $result->where($key, 'like', escape_like($value["filter"]).'%');
}
break;
case "endsWith":
if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        $q->where($caut[1], 'like', '%'.escape_like($value['filter']));
    }
    );
} else {
    $result->where($key, '%'.escape_like($value["filter"]));
}
break;
}
        return $result  ;
    }
    function creezFiltruSAU($key, $value, $q)
    {
        $q->orWhere(function ($result) {
            switch ($value["type"]) {
           case "inRange":
           if (Str::contains($key, ".")) {
               $caut=explode(".", $key);
               $result->whereHas(
                   $caut[0],
                   function ($q) use ($caut, $value) {
                    if ($value["filterType"]=="date") {
                        $q->where($caut[1], '>=', escape_like($value['dateFrom']))
                     ->where($caut[1], '<=', escape_like($value['dateTo']));
                    } else {
                        $q->where($caut[1], '>=', escape_like($value['filter']))
                  ->where($caut[1], '<=', escape_like($value['filterTo']));
                    }
                }
               );
           } else {
             if ($value["filterType"]=="date") {
                 $result->where($key, '>=', escape_like($value["dateFrom"]))
            ->where($key, '<=', escape_like($value["dateTo"])) ;
             } else {
                 $result->where($key, '>=', escape_like($value["filter"]))
            ->where($key, '<=', escape_like($value["filterTo"])) ;
             }
         }

    break;
    case "contains":
    if (Str::contains($key, ".")) {
        $caut=explode(".", $key);
        $result->whereHas(
            $caut[0],
            function ($q) use ($caut, $value) {
            $q->where($caut[1], 'like', '%'.escape_like($value['filter']).'%');
        }
        );
    } else {
        $result->where($key, 'like', '%'.escape_like($value["filter"]).'%');
    }

break;
case "notContains":
if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        $q->where($caut[1], 'not like', '%'.escape_like($value['filter']).'%');
    }
    );
} else {
    $result->where($key, 'not like', '%'.escape_like($value["filter"]).'%');
}
break;

case "equals":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, escape_like($value["dateFrom"]));
    } else {
        $result->where($key, escape_like($value["filter"]));
    }
}

break;
case "greaterThan":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '>', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '>', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '>', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '>', escape_like($value["filter"]));
    }
}

break;
case "greaterThanOrEqual":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '>=', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '>=', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '>=', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '>=', escape_like($value["filter"]));
    }
}

break;
case "lessThan":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '<', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '<', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '<', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '<', escape_like($value["filter"]));
    }
}

break;
case "lessThanOrEqual":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '<=', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '<=', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '<=', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '<=', escape_like($value["filter"]));
    }
}

break;
case "notEqual":

if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        if ($value["filterType"]=="date") {
            $q->where($caut[1], '<>', escape_like($value['dateFrom']));
        } else {
            $q->where($caut[1], '<>', escape_like($value['filter']));
        }
    }
    );
} else {
    if ($value["filterType"]=="date") {
        $result->where($key, '<>', escape_like($value["dateFrom"]));
    } else {
        $result->where($key, '<>', escape_like($value["filter"]));
    }
}

break;
case "startsWith":
if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        $q->where($caut[1], 'like', escape_like($value['filter']).'%');
    }
    );
} else {
    $result->where($key, 'like', escape_like($value["filter"]).'%');
}
break;
case "endsWith":
if (Str::contains($key, ".")) {
    $caut=explode(".", $key);
    $result->whereHas(
        $caut[0],
        function ($q) use ($caut, $value) {
        $q->where($caut[1], 'like', '%'.escape_like($value['filter']));
    }
    );
} else {
    $result->where($key, '%'.escape_like($value["filter"]));
}
break;
}
        });
        return $result  ;
    }
    function aplicaFiltru($result, $searchModel=null, $cautareDupa=null, $sortModel=null, $filterModelArray=null)
    {
        if (!empty($filterModelArray)) {
            foreach ($filterModelArray as $filterModel) {
                if ($filterModel!=null) {
                    foreach ($filterModel as $key => $value) {
                        if (array_key_exists("type", $value)) {
                            $result=creezFiltru($key, $value, $result);
                        } else {
                            $conditie="condition1";

                            $result=creezFiltru($key, $value[$conditie], $result);


                            if ($value['operator']=="OR") {
                                //CONDITION2
                                $result=$result->orWhere(function ($q) use ($key, $value) {
                                    $conditie="condition2";
                                    return creezFiltru($key, $value[$conditie], $q);
                                });
                            } else {
                                //CONDITION2
                                $conditie="condition2";
                                $result=creezFiltru($key, $value[$conditie], $result);
                            }
                        }
                    }
                }
            }
        }
    }
    function isValidEuDate(string $date): bool
    {
        // format strict 00.00.0000
        if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $date)) {
            return false;
        }

        try {
            Carbon::createFromFormat('d.m.Y', $date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    function filterRequest($rez, $searchModel=null, $cautareDupa=null, $sortModel=null, $filterModel=null)
    {
        foreach ($filterModel as &$item) {
            foreach ($item as &$config) {
                if (($config['filterType'] ?? null) === 'boolean') {
                    if ($config['filter']=='Da' || strtoupper($config['filter'])=='TRUE' || $config['filter']=='1') {
                        $config['filter'] = 1;
                    } else {
                        $config['filter'] = 0;
                    }
                }
            }
        }






        $rez=$rez->where(function ($result) use ($searchModel, $cautareDupa, $sortModel, $filterModel) {
            if (strlen($searchModel)==10 && isValidEuDate($searchModel)) {
                $searchModel=Carbon::createFromFormat('d.m.Y', $searchModel)->format('Y-m-d');
            }

            if ($cautareDupa!=null&&escape_like($searchModel)!=null) {
                $coloana=$cautareDupa;
                if (Str::contains($cautareDupa, ".")) {
                    $caut=explode(".", $cautareDupa);
                    $result->whereHas($caut[0], function ($q) use ($caut, $searchModel) {
                        $q->where($caut[1], 'like', '%'.escape_like($searchModel).'%');
                    });
                } else {
                    if ($coloana=="informatii") {
                        $result->whereRaw("MATCH(informatii) AGAINST(? IN NATURAL LANGUAGE MODE)", escape_like($searchModel))
                                                                                                                ->orWhere("informatii", "like", "%".escape_like($searchModel)."%");
                    } else {
                        $result->where($coloana, 'like', '%'.escape_like($searchModel).'%');
                    }
                }
            }

            $result=$result->where(function ($q) use ($searchModel, $cautareDupa, $sortModel, $filterModel) {
                return aplicaFiltru($q, $searchModel, $cautareDupa, $sortModel, $filterModel);
            });
        });

        if ($sortModel[0]["sort"] != null) {
            $colId = $sortModel[0]["colId"];
            $sort = $sortModel[0]["sort"];

            if (Str::contains($colId, ".")) {
                [$relatie, $camp] = explode(".", $colId, 2);

                if ($relatie == 'contract') {
                    // if ($camp == 'nr_contract') {
                    //     $rez = $rez->orderByRaw('CAST(contracte.nr_contract AS UNSIGNED) ' . strtoupper($sort));
                    // }else{
                    //     if ($camp == 'data_contract') {
                    //     $rez = $rez->orderByRaw('CAST(contracte.data_contract AS DATE) ' . strtoupper($sort));
                    //     }else{
                    Log::info("ORDONARE");
                    $rez = $rez->orderBy('contracte.' . $camp, $sort);
                    Log::info($rez->toSql());
                    // }
                 // }
                }
            } else {
                $rez = $rez->orderBy($colId, $sort);
            }
        }



        // if($sortModel[0]["sort"]!=null)
        //     {     if(!Str::contains($sortModel[0]["colId"],".")) {
        //              $rez= $rez->orderBy($sortModel[0]["colId"],$sortModel[0]["sort"]);
        //             }else{
        //               //  $rez= $rez->orderBy($sortModel[0]["colId"],$sortModel[0]["sort"]);
        //             }
        //     }

        return $rez;
    }


    function escape_like($string)
    {
        $search = array('%', '_','ă','Ă','î','Î','ș','Ș','Ț','â','Â','ț','$','&');
        $replace   = array('\%', '\_','\ă','\Ă','\î','\Î','\ș','\Ș','\Ț','\â','\Â','\ț','\$','\&');
        return str_replace($search, $replace, $string);
    }
    /**
     * Generate a URL to a gravatar thumbnail.
     *
     * @param  string $email
     * @return string
     */
    function gravatar_url($email)
    {
        $email = md5($email);

        return "https://gravatar.com/avatar/{$email}?" . http_build_query([
        's' => 60,
        'd' => '../../../../assets/images/portrait/small/avatar-s-11.png'
    ]);
    }

    function userfromPassportToken($token)
    {
        $client = new \GuzzleHttp\Client(['headers' =>
        [
            'Accept'=> '*/*',
            'Authorization'=> 'Bearer '.$token,
        ]
    ]);


        $req = $client->get('http://localhost/api/user');

        $resp = json_decode($req->getBody());

        return $resp;
    }

    function retrieveValidRefreshToken(User $user, Bank $bank)
    {
        $refreshToken=RefreshToken::where('user_id', $user->id)
 ->where('bank_id', $bank->bank_id)
 ->where(DB::raw('DATE_ADD(created_at, INTERVAL refresh_expires_in second)'), '>', Carbon::now())
 ->get()
 ->first();

        return $refreshToken;
    }

    function retrieveValidAccessToken(User $user, Bank $bank)
    {
        $accessToken=RefreshToken::where('user_id', $user->id)
 ->where('bank_id', $bank->bank_id)
 ->where(DB::raw('DATE_ADD(created_at, INTERVAL access_expires_in second)'), '>', Carbon::now())
 ->get()
 ->first();

        if (!$accessToken) {
            $accessToken = refreshAccessToken($user, $bank);
        }
        return $accessToken;
    }

    function refreshAccessToken(User $user, Bank $bank)
    {
        // $bank =  new Bank;
        // $bank=Bank::where("bank_id",$bank->bank_id)->get()->first();
        $refreshToken=retrieveValidRefreshToken($user, $bank);
        $client = new \GuzzleHttp\Client(['headers' => [
      'Content-Type' => 'application/x-www-form-urlencoded',
      'cache-control' => 'no-cache',
      'Postman-Token'=>'f57f9d62-2c8b-4278-891f-9975d8c85805'
  ]]);

        $req = $client->get($bank->idp_sandbox . '/token?grant_type=refresh_token&refresh_token='.$refreshToken->refresh_token.'&client_id='.
        $bank->client_id.'&client_secret='.$bank->client_secret);
        $resp = json_decode($req->getBody());
        $refreshToken->update([
        'access_token'=>$resp->access_token,
        'access_expires_in'=>$resp->expires_in
    ]);

        return $refreshToken;
    }

    function cifra2litere($cifra)
    {
        $litera="";
        if ($cifra=='1') {
            $litera=$litera."o";
        } elseif ($cifra=='2') {
            $litera=$litera."doua";
        } elseif ($cifra=='3') {
            $litera=$litera."trei";
        } elseif ($cifra=='4') {
            $litera=$litera."patru";
        } elseif ($cifra=='5') {
            $litera=$litera."cinci";
        } elseif ($cifra=='6') {
            $litera=$litera."sase";
        } elseif ($cifra=='7') {
            $litera=$litera."sapte";
        } elseif ($cifra=='8') {
            $litera=$litera."opt";
        } elseif ($cifra=='9') {
            $litera=$litera."noua";
        } elseif ($cifra=='10') {
            $litera=$litera."zece";
        } elseif ($cifra=='11') {
            $litera=$litera."unsprezece";
        } elseif ($cifra=='12') {
            $litera=$litera."doisprezece";
        } elseif ($cifra=='13') {
            $litera=$litera."treisprezece";
        } elseif ($cifra=='14') {
            $litera=$litera."paisprezece";
        } elseif ($cifra=='15') {
            $litera=$litera."cincisprezece";
        } elseif ($cifra=='16') {
            $litera=$litera."saisprezece";
        } elseif ($cifra=='17') {
            $litera=$litera."saptesprezece";
        } elseif ($cifra=='18') {
            $litera=$litera."optsprezece";
        } elseif ($cifra=='19') {
            $litera=$litera."nouasprezece";
        } elseif ($cifra=='0') {
            $litera=$litera."";
        }

        return $litera;
    }

    function nr2litere($nr)
    {
        $litere="";
        $nr=str_replace(",", "", $nr);
        $bucati=explode(".", $nr);

        $lungime=strlen($bucati[0]);
        $cifre=str_split($bucati[0]);
        $poz=$lungime;
        for ($i=0;$i<$lungime;$i++) {
            if ($poz==6) {
                if (($cifre[$i]=='1')) {
                    $litere=$litere."osutademii";
                } elseif (($cifre[$i]>'1')) {
                    $rez=cifra2litere($cifre[$i]);
                    $litere=$litere.$rez."sutedemii";
                }
                $poz=$poz-1;
            } elseif ($poz==5) {
                if (($cifre[$i]=='1')) {
                    $litere=$litere."zecemii";
                } elseif (($cifre[$i]>'1')) {
                    $rez=cifra2litere($cifre[$i]);
                    $litere=$litere.$rez."zecidemii";
                }
                $poz=$poz-1;
            } elseif ($poz==4) {
                if (($cifre[$i]=='1')) {
                    $litere=$litere."omie";
                } elseif (($cifre[$i]>'1')) {
                    $rez=cifra2litere($cifre[$i]);
                    $litere=$litere.$rez."mii";
                }
                $poz=$poz-1;
            } elseif ($poz==3) {
                if (($cifre[$i]=='1')) {
                    $rez=cifra2litere($cifre[$i]);
                    $litere=$litere.$rez."suta";
                } elseif (($cifre[$i]>'1')) {
                    $rez=cifra2litere($cifre[$i]);
                    $litere=$litere.$rez."sute";
                } elseif (($cifre[$i]=='0')) {
                    $rez=cifra2litere($cifre[$i]);
                    $litere=$litere.$rez."";
                }
                $poz=$poz-1;
            } elseif ($poz==2) {
                if (($cifre[$i]=='1')) {
                    $rez=cifra2litere($cifre[$i].$cifre[$i+1]);
                    $litere=$litere.$rez."lei";
                    $i=$i+1;
                } elseif (($cifre[$i]>'1') and ($cifre[$i]<>'6')) {
                    $rez=cifra2litere($cifre[$i]);
                    $litere=$litere.$rez."zeci";
                } elseif (($cifre[$i]=='6')) {
                    $litere=$litere."saizeci";
                } elseif (($cifre[$i]=='0')) {
                    $rez=cifra2litere($cifre[$i]);
                    $litere=$litere.$rez."";
                }
                $poz=$poz-1;
            } elseif ($poz==1) {
                if (($cifre[$i]=='1')) {
                    $litere=$litere.$rez."siunleu";
                } elseif (($cifre[$i]=='2')) {
                    $litere=$litere."sidoilei";
                } elseif (($cifre[$i]>'2')) {
                    $rez=cifra2litere($cifre[$i]);
                    $litere=$litere."si".$rez."lei";
                } elseif (($cifre[$i]=='0') and ($lungime>1)) {
                    //$rez=cifra2litere($cifre[$i]);
                    $litere=$litere."leisi";
                } elseif (($cifre[$i]=='0')) {
                    //$rez=cifra2litere($cifre[$i]);
                    $litere=$litere.$rez."zerolei";
                }
                $poz=$poz-1;
            }
        }

        $lungime=strlen($bucati[1]);
        $cifre=str_split($bucati[1]);
        $poz=$lungime;
        if ($lungime==2) {
            $litere=$litere."";
            for ($i=0;$i<$lungime;$i++) {
                if ($poz==2) {
                    if (($cifre[$i]=='1')) {
                        $rez=cifra2litere($cifre[$i].$cifre[$i+1]);
                        $litere=$litere.$rez."bani";
                        $i=$i+1;
                    } elseif (($cifre[$i]=='6')) {
                        $litere=$litere."saizeci";
                    } elseif (($cifre[$i]>'1') and ($cifre[$i]<>'6')) {
                        $rez=cifra2litere($cifre[$i]);
                        $litere=$litere.$rez."zeci";
                    } elseif (($cifre[$i]=='0')) {
                        $litere=$litere."";
                    }
                    $poz=$poz-1;
                } elseif ($poz==1) {
                    if (($cifre[$i]=='1') and ($cifre[$i-1]>1)) {
                        $litere=$litere."siunubani";
                    } elseif (($cifre[$i]=='1')) {
                        $litere=$litere."unban";
                    } elseif (($cifre[$i]=='2') and ($cifre[$i-1]>1)) {
                        $litere=$litere."sidoibani";
                    } elseif (($cifre[$i]=='2')) {
                        $litere=$litere."doibani";
                    } elseif (($cifre[$i]>'2') and ($cifre[$i-1]==0)) {
                        $rez=cifra2litere($cifre[$i]);
                        $litere=$litere.$rez."bani";
                    } elseif (($cifre[$i]>'2')) {
                        $rez=cifra2litere($cifre[$i]);
                        $litere=$litere."si".$rez."bani";
                    } elseif (($cifre[$i]=='0')  and ($cifre[$i-1]>1)) {
                        $rez=cifra2litere($cifre[$i]);
                        $litere=$litere.$rez."bani";
                    } elseif (($cifre[$i]=='0')) {
                        $rez=cifra2litere($cifre[$i]);
                        $litere=$litere.$rez."zerobani";
                    }
                    $poz=$poz-1;
                }
            }
        } else {
            if (($cifre[0]=='1')) {
                $litere=$litere."sizecebani";
            } elseif (($cifre[0]>'1')) {
                $rez=cifra2litere($cifre[0]);
                $litere=$litere."si".$rez."zecibani";
            } elseif (($cifre[0]=='0')) {
                $litere=$litere."sizerobani";
            }
        }
        return $litere;
    }
    function sumainlitere($suma, $tipvaluta)
    {
        $suma=round($suma, 2);

        $intreg=(int)$suma;

        $zecimale= (int) (round((($suma-$intreg)), 2)*100);

        $moneda="";
        $subdiviziune="";
        if (strtoupper($tipvaluta)=="LEI") {
            $moneda=" lei ";
            $subdiviziune=" bani";
        }
        if (strtoupper($tipvaluta)=="EUR") {
            $moneda=" euro ";
            $subdiviziune=" eurocenti";
        }
        if (strtoupper($tipvaluta)=="USD") {
            $moneda=" dolari ";
            $subdiviziune=" centi";
        }
        $rezultat=numarinlitere($intreg).$moneda;
        if ($zecimale!=0) {
            $rezultat=$rezultat." si ".numarinlitere($zecimale).$subdiviziune;
        }
        return $rezultat;
    }
    function numarinlitere($suma)
    {
        $milion = intdiv($suma, 1000000);
        $mie = intdiv($suma, 1000);
        $sute = intdiv($suma, 100);
        $zeci = intdiv($suma, 10);

        $unitati = $suma % 10;
        $rezultat="";
        if ($milion > 0) {
            if (intdiv($milion, 10) >= 2) {
                $rezultat = $rezultat . numarinlitere($milion) . "demilioane" . numarinlitere($suma % 1000000);
            } else {
                if ($milion > 2) {
                    if ($milion <> 16) {
                        $rezultat = $rezultat . numarinlitere($milion) . "milioane" . numarinlitere($suma % 1000000);
                    } else {
                        $rezultat = $rezultat . "saisprezecemilioane" . numarinlitere($suma % 1000000);
                    }
                } else {
                    if ($milion == 1) {
                        $rezultat = $rezultat . "unmilion" . numarinlitere($suma % 1000000);
                    } else {
                        $rezultat = $rezultat . "douamilioane" . numarinlitere($suma % 1000000);
                    }
                }
            }
        } else {
            if ($mie > 0) {
                if (intdiv($mie, 10) >= 2) {
                    if ($mie % 100 <> 16) {
                        $rezultat = $rezultat . numarinlitere($mie) . "mii" . numarinlitere($suma % 1000);
                    } else {
                        $rezultat = $rezultat . "saisprezecemii" . numarinlitere($suma % 1000);
                    }
                } else {
                    if ($mie > 2) {
                        $rezultat = $rezultat . numarinlitere($mie) . "mii" . numarinlitere($suma % 1000);
                    } else {
                        if ($mie == 1) {
                            $rezultat = $rezultat . "omie" . numarinlitere($suma % 1000);
                        } else {
                            $rezultat = $rezultat . "douamii" . numarinlitere($suma % 1000);
                        }
                    }
                }
            } else {
                if ($sute > 0) {
                    if ($sute > 2) {
                        $rezultat = $rezultat . numarinlitere($sute) . "sute" . numarinlitere($suma % 100);
                    } else {
                        if ($sute == 1) {
                            $rezultat = $rezultat . "osuta" . numarinlitere($suma % 100);
                        } else {
                            $rezultat = $rezultat . "douasute" . numarinlitere($suma % 100);
                        }
                    }
                } else {
                    if ($zeci > 0) {
                        $rezultat = $rezultat . ($zeci > 1 ?
        ($zeci==2 ? "doua" : ($zeci==6 ? "sai" : numarinlitere($zeci))) . "zeci" .
        ($suma % 10 > 1 ? "si" . ($suma % 10==2 ? "doi" : numarinlitere($suma % 10)) : numarinlitere($suma % 10)) :
        (numarinlitere($suma % 10) == "" ? "zece" : ($suma == 16 ? "saisprezece" : numarinlitere($suma % 10) . "sprezece")));
                    } else {
                        switch ($unitati) {
    case 1: $rezultat = "unu";
    break;
    case 2: $rezultat = "doi";
    break;
    case 3: $rezultat = "trei";
    break;
    case 4: $rezultat = "patru";
    break;
    case 5: $rezultat = "cinci";
    break;
    case 6: $rezultat = "sase";
    break;
    case 7: $rezultat = "sapte";
    break;
    case 8: $rezultat = "opt";
    break;
    case 9: $rezultat = "noua";
    break;
    default:
      break;
}
                    }
                }
            }
        }
    }

    return $rezultat;
}

// inlocuieste diacriticile si alte caractere cu echivalentul ASCII
function sirfaraspeciale($string)
{
    // http://www.coursesweb.net
    // caractere care trebuie inlocuite cu cele din $add (in aceeasi ordine)
    $rem = array('ă', 'Ă', 'ş', 'Ş', 'ţ', 'Ţ', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ð', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', '§', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', '€', 'Ð', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', '§', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Ÿ'
// aceleasi caractere, dar ca entitati HTML
    , '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&eth;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&oslash;', '&sect;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&yuml;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&AElig;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&euro;', '&ETH;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&Oslash;', '&sect;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&Yacute;', '&Yuml;','&','"');

    // caractere care vor fi adaugate
    $add = array('a', 'A', 's', 'S', 't', 'T', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'ed', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 's', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'EUR', 'ED', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'S', 'U', 'U', 'U', 'U', 'Y', 'Y',
// pentru inlocuit entitatile HTML
    'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'ed', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 's', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'EUR', 'ED', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'S', 'U', 'U', 'U', 'U', 'Y', 'Y',' ','');

    return str_replace($rem, $add, $string);
}

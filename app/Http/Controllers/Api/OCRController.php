<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Imagick;
use GoogleCloudVision\GoogleCloudVision;
use GoogleCloudVision\Request\AnnotateImageRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Carbon;

class OCRController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ocrCI(Request $request)
    {
       
      
        $fileName = "ci_".time();
        $fileExt = $request->file->getClientOriginalExtension();
        $file = $fileName.".".$fileExt;
        $request->file->move(public_path("upload"), $file);
        $path = "/".$file;
       
        $contents = file_get_contents(public_path("upload/". $file));
        $upload = Storage::disk('dropbox')->put($path, $contents);
       // Log::info(Storage::disk('dropbox')->allFiles('/'));
     //   Log::info("UPLOAD:".$upload);
         // $headers = array(
         //      'Content-Type: image/png',
         //    );
         // return  Storage::disk('dropbox')->download($path,"ci_VoineaStefan.png",$headers);
        


        if(strtoupper($fileExt)=="PDF"){
          $imagick = new Imagick();
          $imagick->readImage(public_path("upload/".$file));
          $fileJPG = $fileName.".jpg"; 
          $saveImagePath = public_path("upload/".$fileJPG);
          $imagick->writeImages($saveImagePath, true);
         $file=$fileJPG;
        }
        $img = Image::make(public_path("upload")."/".$file);
     //   $img->resize(408,299);

        // $img->contrast(100);
        $img->save(public_path("upload")."/".$fileName.".png");   
        $fileNou=$fileName.".png";
        $image = base64_encode($img);
        // $fileNou=$file;
        
        $request = new AnnotateImageRequest();
        $request->setImage($image);
        $request->setFeature("TEXT_DETECTION");
        $gcvRequest = new GoogleCloudVision([$request],  env('GOOGLE_CLOUD_KEY'));
        //send annotation request
        $response = $gcvRequest->annotate();
        
       
        $startadresa=false;
        $startSex=false;
        $startValabilitate=false;
        $startNume=false;
        $startPrenume=false;
        $startLocNastere=false;
        $adresa="";
        $cnp="";
        $sex="";
        $seriaCI="";
        $numarCI="";
        $nume="";
        $prenume="";
        $locnastere="";
        $adresa="";
        $emisDe="";
        $dataemitere="";
        $valabilitate="";
        $data_nasterii="";
        $cnpValid="Nu";
        if (count($response->responses) == 1) {
              $text = $response->responses[0]->textAnnotations[0]->description;
          }
          $nr_liniei=0;
          foreach(preg_split("/((\r?\n)|(\r\n?))/", $text) as $line){
      //      Log::info("LINIE ".$line);
            if(Str::startsWith($line,"CNP"))
                { $cnp=substr(trim(str_replace("CNP", "", $line)),0,13);
                  if(validCNP($cnp)) {
                    $cnpValid="Da";
                  } else {
                      $cnpValid="Nu";
                  } 
                 
                }
            if(Str::contains($line,"SERIA")){
                $seriaCI=trim(sirintre($line,"SERIA","NR"));
               
                $numarCI=trim(sirintre($line,"NR","CARD"));
                

            } 
            if(Str::contains($line,"Emis de")||Str::contains($line,"Emisa de")||Str::contains($line,"Delivree")||Str::contains($line,"delivre")||Str::contains($line,"Issued")){
                    $startadresa=false;
                }
              if($startadresa)
              {
                $adresa=trim($adresa." ".trim($line));
              }
                if(Str::contains($line,"Address")||Str::contains($line,"Domiciliu")||Str::contains($line,"Adrese")){
                    $startadresa=true;
                } 

               if($startSex)
                {
                  $sex=trim($line);
                  $startSex=false;
                }
                 if(Str::contains($line,"Sex")){
                    $startSex=true;
                }
                if($startNume)
                {
                  $nume=trim($line);
                  $startNume=false;
                }
                 if(Str::contains($line,"Nume")||Str::contains($line,"Last name")){
                    $startNume=true;
                }
                 if($startPrenume)
                {
                  $prenume=trim($line);
                  $startPrenume=false;
                }
                 if(Str::contains($line,"Preume")||Str::contains($line,"First name")){
                    $startPrenume=true;
                }
                 if($startLocNastere)
                {
                  $locnastere=trim($line);
                  $startLocNastere=false;
                }
                 if(Str::contains($line,"Lieu")||Str::contains($line,"Place of birth")){
                    $startLocNastere=true;
                }
                if($startValabilitate)
                {
                   $arrayDate=explode("-",  $line );
                  $dataemitere=trim($arrayDate[0]??null);
                  $valabilitate=trim($arrayDate[1]??null);
                  $startValabilitate=false;
                }
               if(Str::contains($line,"Valabil")||Str::contains($line,"Valid")){
                    $startValabilitate=true;
                }
                if(Str::contains($line,['SPCLEP','S.P.C.E.P','SPCEP'])){
                    
                    $arrayEmis=explode("\t",  $line );
                   
                    $lung=count($arrayEmis);
                    $emisDe=trim($arrayEmis[0]);
                    
                } 
            } 
           
          if(Str::startsWith($cnp,['1','5'])){

            $sex="M";
          } else{
            $sex="F";
          }
           
          if(Str::startsWith($cnp,['1','2'])){
            $data_nasterii="19".mid($cnp,1,2)."-".mid($cnp,3,2)."-".mid($cnp,5,2);
          }
          if(Str::startsWith($cnp,['5','6'])){
            $data_nasterii="20".mid($cnp,1,2)."-".mid($cnp,3,2)."-".mid($cnp,5,2);
          }
          
          $raspuns=[
                     "cnp"=>$cnp,
                     "seria"=>$seriaCI,
                     "numar"=>$numarCI,
                     "nume"=>$nume,
                     "prenume"=>$prenume,
                     "loc_nastere"=>$locnastere,
                     "adresa"=>$adresa,
                     "emisde"=>$emisDe,
                     "dataemitere"=>($dataemitere?Carbon::parse($dataemitere)->format("Y-m-d"):null),
                     "valabilitate"=>($valabilitate?Carbon::parse($valabilitate)->format("Y-m-d"):null),
                     "data_nasterii"=>$data_nasterii,
                     "sex"=>$sex,
                     "Valid"=> $cnpValid,
                     "filename"=>env("APP_URL")."/upload/".$fileName.".png"
                   ]; 
       
        return json_encode($raspuns);
        
    }
}
   
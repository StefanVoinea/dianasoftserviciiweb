@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație documente primite </h3> </center>
   <center> <h3>{{$selectie}} tip lista  Analitic </h3></center>
    @foreach($antetdocumenteprimite as $document)
                   <table class="table">
      <tr>
        <td width=60%>
        <strong>{{$document->tip_document}}</strong> 
        @if($document->seria)
        Seria: <strong>{{$document->seria}}</strong> 
        @endif
        Numărul: <strong>{{$document->nr_document}}</strong> Data :   <strong>{{dateFormatAfisare($document->data_document)}}</strong><br>
        @if ($document->import)
          Curs valutar {{dateformatAfisare($document->data)}}: 1 {{$document->tip_valuta}} = <strong>{{number_format($document->curs,4)}} Lei</strong>
        @endif
        Gestiune:<strong>{{$document->gestiune["denumire"]}}</strong> <br> 
          
      </td>
      <td width=40%>
          Furnizor: <strong>{{ $document->partener["denumire"] }}</strong>   <br>
          Cod fiscal: <strong>{{ $document->partener["cui"] }}</strong>   <br>
          Reg. com.: <strong>{{ $document->partener["regcom"] }}</strong>   <br>
          Adresa: <strong>{{ $document->partener["adresa"] }}</strong>   <br>
          
      </td>
  </tr>
</table>
<hr>
 <div >
    <table class="table table-condensed" style="border-collapse: collapse; border : 1px solid #a3a3a3;" width=100%  >
            
            <tr >
              <td  align ="center" style="border : 1px solid #a3a3a3;" width="5%">
                    <center>Nr</center>
                    <center>crt.</center>
                </td>
                <td align ="center" style="border : 1px solid #a3a3a3;" width="40%">
                   Denumire
                </td>
                <td  align ="center"  style="border : 1px solid #a3a3a3;" width="5%">
                   Um
                </td>
                
                <td style="border : 1px solid #a3a3a3;" align ="center" width="10%">
                    Cantitate
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" width="10%">
                    <center>Pret intrare</center> 
                </td>
                
                <td style="border : 1px solid #a3a3a3;" align ="center" width="10%">
                    <center>Valoare fara TVA </center> 
               
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center"width="10%">
                    <center>Valoare TVA </center> 
               
                </td>
                 <td style="border : 1px solid #a3a3a3;" align ="center" width="10%">
                    <center>Valoare cu TVA </center> 
               
                </td>
             
            </tr>
            
    
                    @foreach($document->detaliudocumenteprimite as $det_doc)
                      
                      <tr> 
                       <td style="border : 1px solid #a3a3a3;" align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td style="border : 1px solid #a3a3a3;" align="left" width="40%">
                       {{$det_doc["denumire"]  }} <br>
                       {{'cod: '.$det_doc["cod"] .' cont :'.$det_doc["contd"] }} 
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="center" width="5%">
                      <center>{{$det_doc["um"] }} </center>
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="center" width="10%">
                      <center> {{ number_format($det_doc["cantitate"],2)}}</center>
                                         
                      </td>
                      
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($det_doc["pret_intrare"],2),2) }}
                         
                      </td>
                     
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($det_doc["valoare_intrare"],2),2) }}
                         
                      </td>
                       <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($det_doc["valoare_tva_intrare"],2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                        {{ number_format(round($det_doc["valoare_intrare"],2)+round($det_doc["valoare_tva_intrare"],2),2) }}
                      </td>
                      
                      
                    
                          
                      </tr>
                     
                    @endforeach 
             <tr> 
                       <td style="border : 1px solid #a3a3a3;" align="center" colspan="5" width="70%">
                       TOTAL
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($document->detaliudocumenteprimite->sum("valoare_intrare"),2),2) }}
                         
                      </td>
                       <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($document->detaliudocumenteprimite->sum("valoare_tva_intrare"),2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                          {{ number_format(round($document->detaliudocumenteprimite->sum("valoare_intrare"),2)+round($document->detaliudocumenteprimite->sum("valoare_tva_intrare"),2),2) }}
                      </td>
                      
                      
                          
                      </tr>     
                  </table>
     <hr> 
     <br>
      
 </div> 
   @endforeach              
  
    
     

	
	
@stop
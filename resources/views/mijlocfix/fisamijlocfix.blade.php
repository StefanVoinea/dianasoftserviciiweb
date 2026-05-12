@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Fisa mijlocului fix </h3> </center>
   <center> <h3>{{$selectie}} </h3></center>
   <hr>
  
   <h3>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <td align ="left" width="50%">
                 Nr. inventar : {{$mijloacefixe->nr_inventar}} <br><br>
                 Nr. document de provenienta : {{$mijloacefixe->nr_document}} <br><br>
                 Valoare de inventar : {{number_format($mijloacefixe->valoare_de_inventar,2)}} <br><br>
                 Valoare amortizata : {{number_format($mijloacefixe->valoare_amortizata,2)}} <br><br>
                 Valoare de amortizat : {{number_format($mijloacefixe->valoare_de_amortizat,2)}} <br><br>
                 Luni amortizate : {{$mijloacefixe->luni_amortizate}} <br><br>
                 Luni de amortizat : {{$mijloacefixe->luni_de_amortizat}} <br><br>
                 Amortizare lunara : {{number_format($mijloacefixe->amortizare_lunara,2)}} <br><br>
                 Amortizare lunara deductibila: {{number_format($mijloacefixe->amortizare_lunara_deductibila,2)}} <br><br>
                 Denumirea mijlocului fix : <br><br>
                 {{$mijloacefixe->denumire}}
              </td>
              <td align ="left" width="50%">
                   Codul de clasificare : {{$mijloacefixe->cod_clasificare}}<br><br>
                   Data darii in folosinta :                 {{dateFormatAfisare($mijloacefixe->data_punerii_in_functiune)}}<br><br>
                  
                   Data amortizarii complete :
                  {{dateFormatAfisare($mijloacefixe->data_punerii_in_functiune->addYear($mijloacefixe->dnu))}}<br>
                  <br>
                  
                  Durata normala de functionare: {{$mijloacefixe->dnu*12}} luni<br><br>
                  Cota de amortizare: 100 % <br><br>
                  Tip amortizare : LINIARA<br><br>
                  Stare: {{$mijloacefixe->stare}}<br><br>
                  Cont: {{$mijloacefixe->cont}}
              </td>
              
            </tr>
           
     </table>
   </h3>

      <hr>
   <table class="table table-condesed text-sm" border="1" style="border-collapse: collapse;" width="50%" >
            
            <tr style="border-bottom:2pt solid #dab295;">
              <td align ="center" width="20%">
                Data
              </td>
              <td align ="center" width="20%">
                Amortizare
              </td>
              <td align ="center" width="20%">
                Deductibila
              </td>
            </tr>
            @foreach($mijloacefixe->amortizaremf as $amortizare)
              <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
              <td align ="center" width="20%">
                {{dateFormatAfisare($amortizare->data)}}
              </td>
              <td align ="right" width="20%">
                {{number_format($amortizare->valoare,2)}}
              </td>
              <td align ="right" width="20%">
                {{number_format($amortizare->valoare_deductibila,2)}}
              </td>
            </tr>
            @endforeach
   </table>              
      
     <br>
    <table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="center" >
                   ADMINISTRATOR,<br><br>
                   {{$company->director_general}}
                   <br><br>
                    _________________________
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
                     DIRECTOR ECONOMIC,<br><br>
                    {{$company->contabil_sef}}
                   <br><br>
                    ___________________________________
                  </td>
                <td  width="15%" >
                
              </td>
          </tr> 
      </table>   

	
	
@stop
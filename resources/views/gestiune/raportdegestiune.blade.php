@extends('layouts.antet')

@section ('content')
   
   @foreach(collect($miscari)->groupby("data_document") as $raportdata)

   
      <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse; page-break-inside: avoid !important;" width=100%  >
            <thead>
              <tr>
                <td colspan="5">
                  <center>  {{'RAPORT DE GESTIUNE IN DATA DE '.dateFormatAfisare($raportdata[0]->data_document)}}
                  <br>
                 {{' GESTIUNE: '.$gestiune}}  </center>
                  <h4>
                 <br>
                  {{'Sold initial: '. number_format($soldini,2).' LEI '}}
                  </h4>
                </td>
              </tr>
            <tr style="page-break-inside: avoid !important;">
              <th align ="center" colspan="3" width="40%">
                    <center>Document</center>
              </th>
              <th align ="center" rowspan="2" width="40%">
                    Explicatie
              </th>
              <th align ="center" rowspan="2" width="20%">
                    Valoare (Lei)
              </th>
              
            </tr>
            <tr style="page-break-inside: avoid !important;">
              

              <th align ="center"  width="15%">
                    Tip
              </th>
              <th align ="center"  width="10%">
                    Numar
              </th>
              <th align ="center"  width="15%">
                    Data
              </th>
              
            </tr>
           </thead>
                  @foreach($raportdata->groupby("tip") as $raporttip)
                    @foreach($raporttip as $raport)
                      
                      <tr style="page-break-inside: avoid !important;"> 
                       
                       <td align="center" width="15%">
                         {{$raport->tip_document}}
                       </td>
                       <td align="center" width="10%">
                        {{$raport->nr_document}}
                       </td>
                       <td align="center" width="15%">
                        {{dateFormatAfisare($raport->data_document)  }}
                        </td>
                       <td align="left" width="40%">
                         {{ $raport->partener}}
                        
                      </td>
                      <td align="right" width="10%">
                      
                          {{ number_format($raport->total,2) }}
                      
                      </td>
                     
                      </tr>
                     @endforeach
                      <tr style="page-break-inside: avoid !important;"> 
                       
                       <td align="right" colspan="4" width="90%">
                        <strong> {{'TOTAL '.strtoupper($raporttip[0]->tip)}} <strong>
                          <br>
                      </td>
                      <td align="right" width="10%">
                      
                        <strong>  {{ number_format($raporttip->sum('total'),2) }}</strong>
                        <br>
                      
                      </td>
                     
                      </tr>
                   @endforeach 
                   
                    <tr style="page-break-inside: avoid !important;">
                      <td colspan="3">
                         <h4> {{'Sold final: '}}{{number_format($soldini+=collect($raportdata)->where('tip','Intrari')->sum('total')-collect($raportdata)->where('tip','Iesiri')->sum('total'),2)}}{{' LEI'}}
                         </h4>
                      </td>
                      <td align="center" colspan="2">
                        Gestionar, <br>
                        _________________
                      </td>
                    </tr>
               </table>
          <br> <br> 
@endforeach
	
	
@stop
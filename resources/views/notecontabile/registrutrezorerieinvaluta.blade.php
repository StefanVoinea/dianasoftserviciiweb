@extends('layouts.antet')

@section ('content')
   @foreach($notacontabila->groupby("data_nota") as $notadata)
   <center> <h3> {{$denregistru.' PENTRU DATA DE '.dateFormatAfisare($notadata[0]->data_nota)}}  </h3> </center>
   <h3> {{$trezoreria["denumire"]}}</h3>
   <h4>{{'Sold initial: '.number_format($soldini,2).' '.$trezoreria["tip_valuta"]}}</h4>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center"  width="5%">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center"  width="20%">
                    <center>Document <br> (tip,nr,data,partener)</center>
              </th>
              <th align ="center"  width="20%">
                    Explicatie
              </th>
              <th align ="center"  width="10%">
                    Încasări
              </th>
              <th align ="center"  width="10%">
                    Plăți
              </th>
                <th align ="center"  width="5%">
                    Curs 
                </th>
                <th align ="center"  width="10%">
                    Corespondent LEI 
                </th>
                 <th align ="center"  width="10%">
                    Cont corespondent
                </th>
            </tr>
           
     </table>
      <hr>
                   
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($notadata as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      
                       <td align="left" width="20%">
                        @if($nota->tip_doc||$nota->nr_doc||$nota->data_doc)
                        {{$nota->tip_doc.' '.$nota->nr_doc."/".dateFormatAfisare($nota->data_doc)  }}
                        @endif
                        @if($nota->partener)
                        <br>
                        {{$nota->partener  }}
                        @endif
                       </td>
                       <td align="left" width="20%">
                         {{ $nota->expl}}
                        
                      </td>
                      <td align="right" width="10%">
                        @if($nota->contd==$trezoreria['cont'])
                          {{ number_format($nota->suma_valuta,2) }}
                        @endif
                      </td>
                      <td align="right" width="10%">
                        @if($nota->contc==$trezoreria['cont'])
                          {{ number_format($nota->suma_valuta,2) }}
                        @endif
                     </td>
                      <td align="right" width="5%">
                     {{ number_format($nota->curs,4)}}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($nota->suma,2)}}
                         
                      </td>
                      <td align="center" width="10%">
                        @if($nota->contd==$trezoreria['cont'])
                          {{ $nota->contc }}
                        @endif
                        @if($nota->contc==$trezoreria['cont'])
                          {{ $nota->contd }}
                        @endif
                      </td>
                      </tr>
                     @endforeach 
                    </table>
                   
             
   <hr>
     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="45%" align="right" colspan="3">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notadata->where('contd',$trezoreria["cont"])->sum('suma_valuta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                         {{ number_format(round($notadata->where('contc',$trezoreria["cont"])->sum('suma_valuta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="5%" align="right">
                         
                      </td>
                      <td width="10%" align="right">
                         
                      </td>
                      <td width="10%" align="right">
                         
                      </td>
                    </tr>
               </table>
     <hr> 
     <h4>
      {{'Sold final: '}}{{number_format($soldini+=$notadata->where('contd',$trezoreria["cont"])->sum('suma_valuta')-$notadata->where('contc',$trezoreria["cont"])->sum('suma_valuta'),2)}}{{' '.$trezoreria["tip_valuta"]}}
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      {{'  '}}{{number_format($soldiniLEI+=$notadata->where('contd',$trezoreria["cont"])->sum('suma')-$notadata->where('contc',$trezoreria["cont"])->sum('suma'),2)}}{{' LEI'}}
    </h4>
      <hr>
     <br>
     <table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="center" >
                   CASIER,<br><br>
                   
                   <br><br>
                    _________________________
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
                     VERIFICAT,<br><br>
                    
                   <br><br>
                    ___________________________________
                  </td>
                <td  width="15%" >
                
              </td>
          </tr> 
      </table>  
@endforeach
	
	
@stop
@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Cartea mare (Șah) </h3> </center>
   <center> <h3>{{$selectie}} </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center"  colspan="2" width="55%">
                    <center>Cont corespondent</center>
              </th>
              
              <th align ="center" width="10%">
                   Sold initial
              </th>
              <th align ="center" width="10%">
                   Debit
              </th>
                <th align ="center"  width="10%">
                   Credit
              </th>
              <th align ="center"  width="10%">
                   Sold final
              </th>
                
            </tr>
            
     </table>
      <hr>
         
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                
                    @foreach($notacontabila->groupby("contcorespondent['cont']") as $notaCorespondent)
                      @foreach($notaCorespondent as $nota)
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                       <td align="left" width="10%">
                       @if($nota->contd==$cont->cont) 
                         {{ $nota->contc }}
                        @endif
                        @if($nota->contc==$cont->cont) 
                         {{ $nota->contd }}
                        @endif
                      </td>
                       <td align="left" width="45%">
                       
                         {{ $nota->contcorespondent["denumire"]}}
                        
                      </td>
                      
                      
                      <td align="right" width="10%">
                        @if($nota->contd==$cont->cont) 
                          {{ number_format($nota->debit,2)}}
                        @endif
                         
                      </td>
                      <td align="right" width="10%">
                         @if($nota->contc==$cont->cont) 
                              {{ number_format($nota->credit,2)}}
                         @endif
                         
                      </td>
                      <td align="right" width="10%">
                       
                         @if($cont->tip_cont=="Creditor") 
                              {{
                                 number_format($sold+=($nota->credit-$nota->debit),2)
                              }}
                         @endif
                         @if($cont->tip_cont=="Debitor") 
                              {{
                                 number_format($sold+=($nota->debit-$nota->credit),2)
                              }}
                         @endif
                         @if($cont->tip_cont=="Bifunctional") 
                              {{
                                 number_format(abs($sold+=($nota->debit-$nota->credit)),2)
                              }}
                         @endif
                       
                      </td>
                      </tr>
                     @endforeach 
                     @endforeach 
                    </table>
                   
             
   <hr>
     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="60%" align="right" colspan="4">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          @if($cont->tip_cont=="Bifunctional") 
                            
                  {{ number_format(abs($cont->soldinitial),2)}}
                 @endif 
                 @if($cont->tip_cont!="Bifunctional") 
                            
                  {{ number_format($cont->soldinitial,2)}}
                 @endif 
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('debit'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('credit'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                          @if(!$cont->prin_cont)
                          @if($cont->tip_cont=="Bifunctional") 
                            
                              {{ number_format(abs($sold),2)}}
                             @endif 
                             @if($cont->tip_cont!="Bifunctional") 
                                        
                              {{ number_format($sold,2)}}
                             @endif 
                           @endif  
                           </strong>
                         </h4>
                      </td>
                    </tr>
               </table>
     <hr> 
      
     <br>
     <table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="center" >
                   DIRECTOR GENERAL,<br><br>
                   {{$company->director_general}}
                   <br><br>
                    _________________________
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
                     CONTABIL SEF,<br><br>
                    {{$company->contabil_sef}}
                   <br><br>
                    ___________________________________
                  </td>
                <td  width="15%" >
                
              </td>
          </tr> 
      </table>    

	
	
@stop
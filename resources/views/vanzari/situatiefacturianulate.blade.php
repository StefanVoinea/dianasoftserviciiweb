@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație facturi emise anulate</h3> </center>
   <center> <h3>{{$selectie}} </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="40%">
                    Document
                
                </th>
               
                <th align ="center" width="45%">
                    Client
                </th>
             
                <th align ="center" width="10%">
                    Valoare
                </th>
                
                
            </tr>
     </table>
      <hr>
                    @foreach($antetvanzare->groupby("gestiune") as $vanzaregestiune)
                    GESTIUNE {{$vanzaregestiune[0]->gestiune}}
                    <hr>
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($vanzaregestiune as $vanzare)
                       
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="40%">
                       {{$vanzare->tip_document  }}
                      
                      {{$vanzare->seria }}
                    
                      {{$vanzare->numar }}
                      
                      {{dateFormatAfisare($vanzare->data)  }}
                         
                      </td>
                    
                       <td align="left" width="45%">
                      {{ $vanzare->partener }}
                         
                      </td>
                    
                     
                      <td align="right" width="10%">
                     {{ number_format($vanzare->valoare,2)}}
                         
                      </td>
                      </tr>
                     
                     @endforeach
                    </table>
                     <hr> 
     			    
                    @endforeach 
             
   <hr>
   

	
	
@stop

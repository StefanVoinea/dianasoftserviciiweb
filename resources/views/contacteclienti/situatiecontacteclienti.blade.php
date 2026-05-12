@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație contacte clienti </h3> </center>
   <center> <h3>{{$selectie}} </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
                <th align ="center" width="15%">
                   Agentia
                
                </th>
                <th align ="center" width="10%">
                    Data
                </th>
               
                <th align ="center" width="35%">
                    Nume contact
                </th>
               <th align ="center" width="10%">
                    Telefon
                </th>
               <th align ="center" width="10%">
                    E-mail
                </th>
                <th align ="center" width="10%">
                    Oras
                </th>
                 <th align ="center" width="10%">
                    Utilizator
                </th>
                 
            </tr>
         
                    @foreach($contacteclienti as $contact)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                    
                     <td align="left" width="15%">
                      {{ $contact->gestiune }}
                         
                      </td>
                      <td align="left" width="10%">

                         {{dateFormatAfisare($contact->data)  }}
                      </td>
                      
                      <td align="left" width="35%">
                     {{ $contact->nume}}
                         
                      </td>
                      <td align="left" width="10%">
                      {{ $contact->telefon }}
                         
                      </td>
                     <td align="left" width="10%">
                      {{ $contact->email }}
                         
                      </td>
                     
                      <td align="left" width="10%">
                      {{$contact->oras}}
                      </td>
                     
                       <td align="left" width="10%">
                       {{$contact->user["name"]  }}
                      
                      </td>
                     
                      </tr>
                     @endforeach 
                    </table>
                   
     <hr> 
     

	
	
@stop
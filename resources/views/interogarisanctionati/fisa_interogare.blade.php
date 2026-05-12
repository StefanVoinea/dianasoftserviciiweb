@extends('layouts.antet')

@section ('content')

   
    <table class="table">
    	<tr>
    		<td width=100%>
	    		<h3><strong>Interogare in lista cu persoana supuse regimurilor sanctionatorii</strong><br>
				<strong>{{'Agentia:'.$interogare->agentia.' Data interogarii:'.dateFormatAfisare($interogare->data)}}</strong></h3> <br>
				Operator:<strong>{{$interogare->analist_financiar}}</strong> <br> 
			    
			</td>
			
	</tr>
</table>
<hr>
 <div >
 	  <table class="table table-condensed" style="border-collapse: collapse; border : 1px solid #a3a3a3;" width=100%  >
            
            <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                    Nume client
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{$interogare->nume_client}}
                </td>
              
            </tr>
            <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                   Cuvant cheie
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{$interogare->cuvant_cheie}}
                </td>
              
            </tr>
             <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                   Rezolutie UE
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{$interogare->rezolutie_analist}}
                </td>
              
            </tr>
            <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                   Rezolutie OFAC
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{$interogare->rezolutie_analist_sua}}
                </td>
              
            </tr>
            <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                   Rezolutie ONU
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{$interogare->rezolutie_analist_onu}}
                </td>
              
            </tr>
             <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                   Operator
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{$interogare->operator}}
                </td>
              
            </tr>
            <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                   Verificare agentie
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{$interogare->verificare_agentie}}
                </td>
              
            </tr>
            <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                   Data aprobarii RPSB Central
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{dateFormatAfisare($interogare->data_aprobarii)}}
                </td>
              
            </tr>
             <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                   Rezolutie aprobare RPSB Central
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{$interogare->rezolutie_aprobare}}
                </td>
              
            </tr>
            <tr >
              <td  align ="left" style="border : 1px solid #a3a3a3;"  width="20%">
                   Status RPSB central
                </td>
                <td align ="left" style="border : 1px solid #a3a3a3;"  width="80%">
                   {{$interogare->status}}
                </td>
              
            </tr>
      </table>   
 </div>	
	  
	   

	
	
@stop
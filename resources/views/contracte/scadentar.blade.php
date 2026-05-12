@extends('layouts.antet')

@section ('content')

   
    <table class="table">
    	<tr>
    		<td width=60%>
	    		<h2><strong>ANEXA 2 - Scadențar plăți</strong></h2>
				
				Număr contract: <strong>{{$contract->nr_contract}}</strong><br>
				Data contract:   <strong>{{dateFormatAfisare($contract->data_contract)}}</strong> <br>
				@if($contract->locdeveci)
				Loc de veci: <strong>{{ $contract->locdeveci->identificator }}</strong> <br>
				@endif
		    	
			</td>
			<td width=40%>
			      Client: <strong>{{ $contract->nume }}</strong>   <br>
				  CNP: <strong>{{ $contract->cnp }}</strong>   <br>
				  C.I.: <strong>{{ $contract->ci_numar }}</strong>   <br>
				  Adresa: <strong>{{ $contract->adresa }}</strong>   <br>
				  Telefon: <strong>{{ $contract->telefon }}</strong> <br>
				 
			</td>
	</tr>
</table>
<hr>
 <div >
       	   
	  </div>	
	  
	    @include('partials/detaliuscadentarcontract') 
	  <hr>
	  
	

	
	
@stop
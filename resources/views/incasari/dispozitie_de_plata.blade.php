@extends('layouts.antet_chitanta')

@section ('content')
         <hr>  
    <table class="table" width="80%">
    	<tr>
    		<td width=100% align="center">
	    		<h3><strong>DISPOZIȚIE DE PLATĂ</strong> Număr: <strong>{{$incasare[0]->nr_document}}</strong>
				Data:   <strong>{{dateFormatAfisare($incasare[0]->data_document)}}</strong></h3>
				
			</td>
			
	</tr>
</table>
<hr>
<table class="table" width="80%">
	<tr>
    		<td >
	    		
				Beneficiarul sumei: <strong>{{ $incasare[0]->partener }}</strong>  C.N.P.: <strong>{{ $incasare[0]->cui }}</strong>   <br>
				Suma : <strong>{{number_format(abs($incasare->sum('suma')),2).' LEI adica '.nr2litere(number_format(abs($incasare->sum('suma')),2))}}</strong><br>
				Scopul plății:   <strong>{{'Restituire contravaloare facturi'}}</strong> <br>
		    	
			</td>
			
	</tr>
</table>
<hr>
<table clas="table" width="80%">
	  <tr>
                  <td width="50%" align="center" >
                    Semnătura de primire<br><br>
                    _________________________
                  </td>
                   <td width="50%" align="center" >
                    Semnătura casierului<br><br>
                    ___________________________________
                  </td>
                  
          </tr> 
</table>
<hr>
 <div >
       	   
	  </div>
	  @stop
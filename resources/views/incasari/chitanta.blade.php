@extends('layouts.antet_chitanta')

@section ('content')
         <hr>  
    <table class="table" width="80%">
    	<tr>
    		<td width=100% align="center">
	    		<h3><strong>CHITANȚA</strong> Număr: <strong>{{$incasare[0]->nr_document}}</strong>
				Data:   <strong>{{dateFormatAfisare($incasare[0]->data_document)}}</strong></h3>
				
			</td>
			
	</tr>
</table>
<hr>
<table class="table" width="80%">
	<tr>
    		<td >
	    		
				Am primit de la : <strong>{{ $incasare[0]->partener }}</strong>  C.U.I./C.N.P.: <strong>{{ $incasare[0]->cui }}</strong>   <br>
				Suma : <strong>{{number_format($incasare->sum('suma'),2).' LEI adica '.sumainlitere($incasare->sum('suma'),"Lei")}}</strong><br>
				Reprezentând:   <strong>{{'Contravaloare facturi'}}</strong> <br>
		    	
			</td>
			
	</tr>
</table>
<hr>
<table clas="table" width="80%">
	  <tr>
                  <td width="50%" align="center" >
                   
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
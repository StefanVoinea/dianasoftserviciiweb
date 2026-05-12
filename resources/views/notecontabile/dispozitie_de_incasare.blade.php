@extends('layouts.antet_chitanta')

@section ('content')
         <hr>  
    <table class="table" width="80%">
    	<tr>
    		<td width=100% align="center">
	    		<h3><strong>DISPOZITIE DE INCASARE CATRE CASIERIE</strong> Numar: <strong>{{$dispozitie->nr_doc}}</strong>
				Data:   <strong>{{dateFormatAfisare($dispozitie->data_doc)}}</strong></h3>
				
			</td>
			
	</tr>
</table>
<hr>
<table class="table" width="80%">
	<tr>
    		<td >
	    		
				Numele si prenumele: <strong>{{ $dispozitie->partener }}</strong>  C.N.P.: <strong>{{ $dispozitie->partenerul["cui"] }}</strong>   <br>
				Suma : <strong>{{number_format(abs($dispozitie->suma),2).' LEI adica '.nr2litere(number_format(abs($dispozitie->suma),2))}}</strong><br>
				Scopul incasarii:   <strong>{{$dispozitie->expl}}</strong> <br>
		    	
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
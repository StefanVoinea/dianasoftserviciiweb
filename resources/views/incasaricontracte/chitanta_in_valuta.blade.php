<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    

    <!-- CSRF Token -->
    
   

    <!-- Styles -->
    
    <link href="{{env('APP_URL').'/css/main.css'}}" rel="stylesheet" type="text/css">
    
    <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css"> -->
     <style>
    @page { size: A4; margin: 12mm 10mm; }

    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }

    /* background logo */
    .bg-logo {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      z-index: 0;
      pointer-events: none;
      opacity: 0.06;

      background-image: url("{{env('APP_URL').'/images/logo/'.$company->slug.'/watermark_logo.png'}}");
      background-repeat: repeat;
      background-position: top center;
      background-size: 100%;
      opacity: 0.07;
	  z-index: 0;
	  pointer-events: none;
    }
    .wm-text {
    position: relative;

    inset: -200px;
    z-index: 1;
    pointer-events: none;
    opacity: 0.12;
    transform: rotate(-25deg);

    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;

    font-size: 24px;
    font-weight: bold;
    letter-spacing: 1px;
    color: #000;
  }


    /* tot conținutul peste background */
    .content {
      position: relative;
      z-index: 1;
    }
  </style>

     <style type="text/css">
     .bodyfactura { 
        padding-bottom: 40px;
        background-color: white;
        color:black;
        font-family:cambria;

      }
      /*.sidebar-nav {
        padding: 9px 0;

        }*/
    /*    table, th, td {
    border: 1px solid black;*/
}
      
    </style>
    <style>
      .antet {
 
  display:flex ;
  justify-content: space-between;
  font-size: 16px;
}
hr { 
   
    /*margin-top: 0.5em;
    margin-bottom: 0.5em;
    margin-left: auto;
    margin-left: auto;
    border-width: 4px;
    font-color: #ff9933;
    border-color: : #ff9933;
    background-color: #ff9933;*/
    border: 2px solid #6d6fe3;
    
} 

</style>
    <!-- Scripts -->
   
    

</head>
<body class="bodyfactura">
<div class="bg-logo"></div>       
   @for($exemplar = 1; $exemplar <= 2; $exemplar++)    
            <div class="container"  style="page-break-before: always;">
              
           
<hr> 
       <table class="table table-condesed" width="100%"  >
            
            <tr>
              <td align ="left" width="60%">
        <div >
      <!-- <h4> -->
      <div >
        
      <div>
      <h3>
      <strong> {{$company->denumire}}</strong>
      </h3>
      <div>
      Sediul:<strong>{{$company->adresa}}</strong> 
      </div>
      <div>
       C.U.I.: <strong>{{$company->cui}}</strong>
       </div>
      <div>
       Reg.com.:<strong> {{$company->regcom}}</strong>  
      </div>
      <div>
      RGBNR::<strong>{{$company->nrautorizatie}}</strong>
      
      Email:<strong>{{$company->email}}</strong>
      </div>
     
     <div>

      Capital social:<strong>{{$company->capital_social}}</strong>
      
      </div>
      <div>
                Punct de lucru:<strong>{{$agentia->adresa}}</strong>
      </div>
   
      </div>
  <!-- </h4> -->
</div>
</td>
<td align ="center" width="40%">
     <div class="mt-3 mr-24">
      
       <img   src="{{env('APP_URL').'/images/logo/'.$company->slug.'/logo.png'}}" alt="logo" />
      <!-- <a class="navbar-brand" href="{{ url('/') }}">
                    <img   width=100% height=100% src="/logo_alprof.png" alt="logo" />
                    </a>  
                    \
       -->
       <br><br><br><br>
          <strong>Exemplar nr.: {{$exemplar}} </strong> <br>
         Data listarii:<strong>{{$datalistarii}}</strong>
   </div>
 </td>
 </tr>
 </table>      
   
</div>
         <hr>  
          

         
    <table class="table" width="100%">
    
    	<tr>
    		<td width=100% align="center">
	    		<h3><strong>CHITANȚA</strong> Seria <strong>{{$incasare[0]->seria}}</strong> Numărul: <strong>{{$incasare[0]->nr_document}}</strong>
				Data:   <strong>{{dateFormatAfisare($incasare[0]->data_document)}}</strong></h3>
				
			</td>
			
	</tr>
</table>
<hr>
<table class="table" width=100%>
	<tr>
		    <td width=10%>
		    </td >	
    		<td  width=90%>
	    		
				Am primit de la : <strong>{{ $incasare[0]->nume }}</strong>  C.U.I./C.N.P.: <strong>{{ $incasare[0]->contract->cnp }}</strong>   <br><br>
				Adresa : <strong>{{$incasare[0]->contract->adresa}}</strong><br><br>
				Suma : <strong>{{number_format($incasare->sum('suma_valuta'),2).' '.$incasare[0]->tip_valuta.' adica '.sumainlitere($incasare->sum('suma_valuta'),$incasare[0]->tip_valuta)}}</strong><br>
				<br>
				@if($incasare[0]->nomtertiincasare)
				 Prin tert: {{$incasare[0]->nomtertiincasare->nume_tert.", ".$incasare[0]->nomtertiincasare->cnp_tert}}
				@endif
				 @if($incasare[0]->anulat)	 
					<center><h1 >ANULAT</h1></center>
				 @else
				 <br><br>
				 @endif 
		    	 Conform anexa
			</td>
			
	</tr>
	  <tr>
                  <td width="10%" align="left" >
                   
                  </td>
                   <td width="90%" align="center" >
                    Semnătura casierului<br><br>
                    ___________________________________
                  </td>
                  
          </tr> 
</table>
<hr>
 <div class="wm-text">
    EXEMPLAR ORIGINAL • {{ $user->name }} • • {{ now()->format('d.m.Y H:i:s') }}
  </div>
 <div >
        <table class="table table-condesed"  style="border-collapse: collapse; " width="100%">
        <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
    		<td width=100% colspan="7" align="center">
	    		<h3>ANEXA LA CHITANTA NR.:{{$incasare[0]->nr_document." / ".dateFormatAfisare($incasare[0]->data_document)}}</h3>
			</td>

    	<tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
    		<td width=10% align="center">
	    		Nr contract
			</td>
			<td width=10% align="center">
	    		Data contract
			</td>
			<td width=10% align="left">
	    		Explicatie
			</td>
			<td width=10% align="center">
	    		Data scadenta
			</td>
			<td width=10% align="right">
	    		Suma LEI
			</td>
			<td width=10% align="right">
	    		Curs
			</td>
			<td width=10% align="right">
	    		Suma EUR
			</td>
		</tr>
         @foreach($incasare as $linie)
         <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
    		<td width=10% align="center">
	    		{{$linie->contract->nr_contract}}
			</td>
			<td width=10% align="center">
	    		{{dateFormatAfisare($linie->contract->data_contract)}}
			</td>
			<td width=10% align="left">
	    		{{($linie->explicatie)}}
			</td>
			<td width=10% align="center">
	    		{{dateFormatAfisare($linie->data_scadenta)}}
			</td>
			<td width=10% align="right">
	    		{{number_format($linie->suma,2)}}
			</td>
			<td width=10% align="right">
	    		{{number_format($linie->curs,4)}}
			</td>
			<td width=10% align="right">
	    		{{number_format($linie->suma_valuta,2)}}
			</td>
		</tr>
		@endforeach
		<tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
    		<td width=10% colspan="4" align="center">
	    		<h3>TOTAL :</h3>
			</td>
			<td width=10% align="right">
	    		<h3>{{number_format($incasare->sum('suma'),2)." LEI"}}</h3>
			</td>
			<td width=10% align="right">
	    		
			</td>
			<td width=10% align="right">
	    		<h3>{{number_format($incasare->sum('suma_valuta'),2)." EUR"}}</h3>
			</td>
		</tr>
</table>	   
 </div>
 @endfor	

</body>
</html>
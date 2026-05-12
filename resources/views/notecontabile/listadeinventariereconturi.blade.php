@extends('layouts.antet')

@section ('content')

    @foreach($tabel->groupBy("agentia") as $agentia)
     @foreach($agentia->groupBy("tip_valuta") as $tipvaluta)
      @foreach($tipvaluta->groupBy("cont") as $cont)
          <table class="table" width=100%>
	     	<tr >
	     		<td width=40%> </td>
	     		<td align="center" width=20%> LISTA DE INVENTARIERE <br> DATA DE : {{dateFormatAfisare($data)}}</td>
	     		<td width=20%> 

	     			GESTIUNE:{{$cont[0]->agentia}}  <br>
	     			TIP VALUTA:{{$cont[0]->tip_valuta}}  <br>	
	     			
	     		</td>
	     		<td width=20%> 
	     			CONT:{{$cont[0]->cont}}<br>
	     			
	     		</td>
	     	</tr>	
	    </table>
	   <hr>
	       <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100%>
            
            <tr >
              <td align ="center" rowspan="3" width="5%">
               Nr.crt.
              </td>
              <td align ="center" rowspan="3" width="31%">
                 Denumirea bunurilor inventariate   
              </td>
              <td align ="center" rowspan="3" width="10%">
                   Codul sau numarul de inventar
              </td>
               <td align ="center" rowspan="3" width="5%">
                   Cont ctb.
              </td>
              <td align ="center" colspan="4" width="16%">
                   CANTITATI
              </td>
               <td align ="center" rowspan="3"  width="5%">
                   Pret unitar
              </td>   
              <td align ="center" colspan="3" width="16%">
                   VALOAREA CONTABILA
              </td>    
             <td align ="center"  rowspan="3" width="10%">
                   Valoarea de inventar 
              </td>
              <td align ="center" colspan="2" rowspan="2"width="10%">
                   DEPRECIEREA 
              </td>        
            </tr>

            <tr >
             
              <td align ="center" colspan="2" width="10%">
                   Stocuri
              </td>
              <td align ="center" colspan="2" width="6%">
                   Diferente
              </td>
               
               
              <td align ="center" rowspan="2" width="6%">
                   VALOAREA LEI
              </td>    
              <td align ="center" colspan="2" width="6%">
                   Diferente
              </td>    
              </tr>
           
            <tr >
             
              <td align ="center"  width="5%">
                   Faptice
              </td>
              <td align ="center"  width="5%">
                   Scriptice
              </td>
               <td align ="center"  width="3%">
                   Plus
              </td>
              <td align ="center"  width="3%">
                   Minus
              </td>
                <td align ="center"  width="3%">
                   Plus
              </td>
              <td align ="center"  width="3%">
                   Minus
              </td>
               <td align ="center" class="text-sm"  width="5%">
                   Valoarea
              </td>
              <td align ="center"  width="4%">
                   Motivul
              </td> 
              </tr>
              <tr>
              	<td align="center" width="5%">0</td>
              	<td align="center" width="10%">1</td>
              	<td align="center" width="10%">2</td>
              	<td align="center" width="5%">3</td>
              	<td align="center" width="5%">4</td>
              	<td align="center" width="5%">5</td>
              	<td align="center" width="5%">6</td>
              	<td align="center" width="10%">7</td>
              	<td align="center" width="6%">8</td>
              	<td align="center" width="5%">9</td>
              	<td align="center" width="5%">10</td>
              	<td align="center" width="5%">11</td>
              	<td align="center" width="16%">12</td>
              	<td align="center" width="5%">13</td>
              	<td align="center" width="4%">14</td>
              </tr>	
            
         @foreach($cont as $linie)      
          <tr >
          	<td align="center" width="3%"> {{$i++}}</td>
          	<td align="left" width="10%"> {{$linie->nume}}</td>
          	<td align="center" width="10%"> {{$linie->nr_contract}}</td>
          	<td align="center" width="5%"> {{$linie->cont}}</td>
          	<td align="right" width="5%"> {{$linie->sold}}</td>
          	<td align="right" width="5%"> {{$linie->sold}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> {{$linie->curs}}</td>
          	<td align="right" width="5%"> {{$linie->sold_lei}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	<td align="right" width="5%"> {{$linie->sold_lei}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	
          </tr>	
	    @endforeach
	  
	  <tr >
          	<td colspan="9"> TOTAL {{$cont[0]->cont}}</td>
          	<td align="right" width="5%"> {{$cont->sum("sold_lei")}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	<td align="right" width="5%"> {{$cont->sum("sold_lei")}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	
          </tr>	
	 @endforeach   
	 <tr >
          	<td colspan="9"> TOTAL {{$tipvaluta[0]->tip_valuta}}</td>
          	<td align="right" width="5%"> {{$tipvaluta->sum("sold_lei")}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	<td align="right" width="5%"> {{$tipvaluta->sum("sold_lei")}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	
          </tr>	
    @endforeach
     <tr >
          	<td colspan="9"> TOTAL {{$agentia[0]->agentia}}</td>
          	<td align="right" width="5%"> {{$agentia->sum("sold_lei")}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	<td align="right" width="5%"> {{$agentia->sum("sold_lei")}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	
          </tr>	
   @endforeach
    <tr >
          	<td colspan="9"> TOTAL GENERAL</td>
          	<td align="right" width="5%"> {{$tabel->sum("sold_lei")}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	<td align="right" width="5%"> {{$tabel->sum("sold_lei")}}</td>
          	<td align="center" width="5%"> </td>
          	<td align="center" width="5%"> </td>
          	
          </tr>	
  </table>        

	
@stop
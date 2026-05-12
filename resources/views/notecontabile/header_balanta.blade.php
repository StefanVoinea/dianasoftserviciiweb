<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{env('APP_URL').'css/main.css'}}" rel="stylesheet" type="text/css">
      <style type="text/css">
     .bodyfactura { 
        padding-bottom: 10px;
        background-color: white;
        color:black;
        font-family:cambria;

      }
    
}
      
    </style>
    <style>
      .antet {
               display:flex ;
              justify-content: space-between;
              font-size: 16px;
            }
hr { 
   
    border: 2px solid #dab295;
    
} 

</style>
</head>
<body class="bodyfactura">
  <div class="container">
              
           
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
       Reg.com.:<strong> {{$company->regcom}}</strong>  C.U.I.: <strong>{{$company->cui}}</strong>
      </div>
      <div>
      Adresa:<strong>{{$company->adresa}}</strong> 
      </div>
      <div>
      Telefon:<strong>{{$company->telefon}}</strong>
      
      Email:<strong>{{$company->email}}</strong>
      </div>
      <div>

      Banca:<strong>{{$company->banca}}</strong>
      Cont:<strong>{{$company->cont}}</strong>
      </div>
     <div>

      Capital social:<strong>{{$company->capital_social}}</strong>
      
      </div>
      </div>
  <!-- </h4> -->
</div>
</td>
<td align ="center" width="40%">
     <div class="mt-3 mr-24">
      
       <img   src="{{env('APP_URL').'images/logo/'.$company->slug.'/logo.png'}}" alt="logo" />
      <!-- <a class="navbar-brand" href="{{ url('/') }}">
                    <img   width=100% height=100% src="/logo_alprof.png" alt="logo" />
                    </a>  
       --></div>
 </td>
 </tr>
 </table>      
   
</div>
                <hr>  
          

             <center> <h3> Balanța contabilă {{$selectie}} </h3></center>
   <hr>
      <table class="table table-condesed" style="border-collapse: collapse;" width=100%>
            
            <tr >
              <th align ="center"  rowspan="2" width="5%">
                    <center>Cont</center>
              </th>
              <th align ="center"  rowspan="2" width="10%">
                    <center>Denumire</center>
              </th>
              <th align ="center" colspan="2" width="16%">
                   Solduri de deschidere
              </th>
               <th align ="center" colspan="2" width="16%">
                   Anterior
              </th>
              <th align ="center" colspan="2" width="16%">
                   În lună
              </th>
               <th align ="center" colspan="2" width="16%">
                   Total
              </th>   
              <th align ="center" colspan="2" width="16%">
                   Sold
              </th>    
             <!--   <th align ="center"  rowspan="2" width="5%">
                    <center>Cont</center>
              </th> -->
            </tr>
            <tr >
              <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
            </tr>

      </table>
      <hr>
    </body>
    </html>

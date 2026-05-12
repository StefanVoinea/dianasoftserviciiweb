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
    <style type="text/css" >
          div.page
          {
              page-break-after: always;
              page-break-inside: avoid;
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
    border: 2px solid #dab295;
    
} 

</style>
    <!-- Scripts -->
   
    

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
      Banca:<strong>{{$company->banca_valuta}}</strong>
      Cont:<strong>{{$company->cont_valuta}}</strong>
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
      
       <img   src="{{env('APP_URL').'/images/logo/'.$company->slug.'/logo.png'}}" alt="logo" />
      <!-- <a class="navbar-brand" href="{{ url('/') }}">
                    <img   width=100% height=100% src="/logo_alprof.png" alt="logo" />
                    </a>  
       --></div>
 </td>
 </tr>
 </table>      
   
</div>
         <hr>  
          
            
            @yield('content')
   

   

</body>
</html>
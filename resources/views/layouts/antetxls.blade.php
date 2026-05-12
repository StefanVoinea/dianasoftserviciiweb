<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{env('APP_URL').'/css/main.css'}}" rel="stylesheet" type="text/css">
     <style type="text/css">
     .bodyfactura { 
        padding-bottom: 40px;
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
              
           

       <table class="table table-condesed"  width=100>
            
            <tr>
              <td align ="left" colspan="15" width=100>
              <h3><strong> {{$company->denumire}}</strong></h3>
              </td>
           </tr>   
           <tr>
              <td align ="left" colspan="15" width=100>
                    Reg.com.:<strong> {{$company->regcom}}</strong>  C.U.I.: <strong>{{$company->cui}}</strong>
              </td>
           </tr>     
      <tr>
              <td align ="left" colspan="15" width=100>
              Adresa:<strong>{{$company->adresa}}</strong> 
              </td>
          </tr>
      <tr>
              <td align ="left" colspan="15" width=100>
      Telefon:<strong>{{$company->telefon}}</strong>
      
      Email:<strong>{{$company->email}}</strong>
      </td>
        </tr>
      <tr>
              <td align ="left" colspan="15" width=100>

      Banca:<strong>{{$company->banca}}</strong>
      Cont:<strong>{{$company->cont}}</strong>
      </td>
    </tr>
    <tr>
              <td align ="left" colspan="15" width=100>
      Banca:<strong>{{$company->banca_valuta}}</strong>
      Cont:<strong>{{$company->cont_valuta}}</strong>
      </td>
     </tr> 
     <tr>
              <td align ="left" colspan="15" width=100>

      Capital social:<strong>{{$company->capital_social}}</strong>

</td>
<!-- <td align ="center" >
     <div class="mt-3 mr-24">
 
       <img   src="{{env('APP_URL').'images/logo/'.$company->slug.'/logo.png'}}" alt="logo" /> 
       <a class="navbar-brand" href="{{ url('/') }}">
                    <img   width=100% height=100% src="/logo_alprof.png" alt="logo" />
                    </a>  
   </div>
 </td> -->
 </tr>
 </table>      
   
</div>
       

            @yield('content')
   

   
    
</body>
</html>
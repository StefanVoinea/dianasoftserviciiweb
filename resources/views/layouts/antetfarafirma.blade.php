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
     .antet {
 
          display:flex ;
          justify-content: space-between;
          font-size: 16px;
        }
    hr { 
   
        border: 2px solid #6d6fe3;
        
    } 

</style>
    <!-- Scripts -->
   
    

</head>
<body class="bodyfactura">

       
         
          

            @yield('content')
   

   
    
</body>
</html>
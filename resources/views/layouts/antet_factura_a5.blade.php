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

       
            <div class="container">
              
           

          
            </div>
            @yield('content')
   

   
    
</body>
</html>
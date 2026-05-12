<table >
         <tr>
       
        <td></td>
        <td colspan="8">
        <b> SITUATIE VALORI LA SFARSITUL ZILEI </b>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
      </tr>  
     
      <tr>
       
        <td>         
          CURS BNR
        </td>
        <td>
          DATA     
        </td>
        <td colspan="2">
          VALOARE CREDITE
        </td>
        <td colspan="2" >
          VALOARE DOBANZI
        </td>
        <td colspan="2">
          RATA PRINCIPAL
        </td>
        <td colspan="2" >
          VALORI JUSTE
        </td>
        <td colspan="2">
         VALORI JUSTE LEI
        </td>
        <td >
          NR CONTRACTE
         </td>
        
      </tr>    
   
        @foreach ($arhivaValori as $linie)
          <tr>
            <td>         
              {{"1 euro=".$linie->curs}}
            </td>
            <td>
              {{dateFormatAfisare($linie->data)}}      
            </td>
            <td>
              {{$linie->valoare_credite}}      
            </td>
            <td>
             </td> 
            <td >
              {{$linie->valoare_dobanzi}}      
            </td>
            <td>
             </td> 
            <td >
              {{$linie->rata_principal}}
            </td>
            <td>
             </td> 
            <td >
              {{$linie->valori_juste}}
            </td>
            <td>
             </td> 
            <td>
            {{$linie->valori_juste_lei}}
            </td>
            <td>
             </td> 
            <td >
            {{$linie->nr_contracte}}
            </td>
            
          </tr>
          @endforeach
         
</table>

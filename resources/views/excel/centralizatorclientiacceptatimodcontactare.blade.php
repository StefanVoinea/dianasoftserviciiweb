<table >
       
      <tr>
       
        <td>         
          
        </td>
        <td>
          MOD CONTACTARE
        </td>
        @foreach (collect($contracte)->groupBy("agentia") as $coloana)
        <td>
          {{$coloana[0]->agentia}}
        </td>
        @endforeach
        <td >
          TOTAL %
        </td>
      </tr>    
   
        @foreach (collect($contracte)->groupBy("sursa_de_informare") as $linie)
          <tr>
            <td>         
              
            </td>
            <td>
              {{$linie[0]->sursa_de_informare}}      
            </td>
            @foreach (collect($contracte)->groupBy("agentia") as $coloana)
              <td>
                {{collect(
                    (collect($contracte)->where("sursa_de_informare",$linie[0]->sursa_de_informare)->where("agentia",$coloana[0]->agentia)->values()))->count()}}
              </td>
              @endforeach
              <td >

                 {{100*((
                          collect(
                                    collect($contracte)->where("sursa_de_informare",$linie[0]->sursa_de_informare)->values()
                                  )->count()
                        )/(collect($contracte)->count())
                        )}}
              </td>
          </tr>
          @endforeach
          <tr>
            <td>         
              
            </td>
            <td>
              TOTAL
            </td>
            @foreach (collect($contracte)->groupBy("agentia") as $coloana)
              <td>
                {{collect(
                          collect($contracte)->where("agentia",$coloana[0]->agentia)->values()
                          )->count()}}
              </td>
              @endforeach
              <td >

                 100.0000
              </td>
          </tr>
</table>
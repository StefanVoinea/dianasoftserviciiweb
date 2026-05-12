
<table class="table">
    <tr>
       
        <th colspan='{{count($antetTabel)}}'>
        
        </th>
      </tr>
      <tr>
       
        <th colspan='{{count($antetTabel)}}'>
        
        </th>
      </tr>
      <tr>
       
        <th colspan='{{count($antetTabel)}}'>
        
        </th>
      </tr>
      <tr>
       
        <th colspan='{{count($antetTabel)}}'>
        
        </th>
      </tr>
      <tr>
       
        <th colspan='{{count($antetTabel)}}'>
          {{"Unitatea:".$company->denumire}}
        </th>
      </tr>
       
      <tr>
       
        <th colspan='{{count($antetTabel)}}'>
          {{"Localitatea:".$company->localitate}}
        </th>
      </tr>
       <tr>
        <th colspan='{{count($antetTabel)}}'>
         
        </th>
      </tr>
      <tr>
        <th align="right" colspan='{{count($antetTabel)}}'>
          {{"Nr:"}}
        </th>
      </tr>
    <thead>
    <tr>
        @foreach ($antetTabel as $col)
        <th>
            
            {{$col["denumire"]}}
          
        </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
       
            @foreach($tabel as $rand)
             
              @if(count($groupBy)>=1) 
                                  <!-- GRUP 1 -->
                <tr>
                 <td>
                  {{$groupBy[0]["type"]==="Date"
                    ?$groupBy[0]["denumire"].": ".dateFormatAfisare(collect($rand->flatten()->values()->all()[0])[$groupBy[0]["col"]])
                    :$groupBy[0]["denumire"].": ".collect($rand->flatten()->values()->all()[0])[$groupBy[0]["col"]]}}
                 </td>
               </tr>
               @foreach($rand as $randGrup)
                <!-- GRUP 2 -->
                
                  @if(count($groupBy)>=2)                
                <tr>
                 <td>
                    {{$groupBy[1]["type"]==="Date"
                     ?$groupBy[1]["denumire"].": ".dateFormatAfisare(collect($randGrup->flatten()->values()->all()[0])[$groupBy[1]["col"]])
                     :$groupBy[1]["denumire"].": ".collect($randGrup->flatten()->values()->all()[0])[$groupBy[1]["col"]]}}
                 </td>
               </tr>
               @foreach($randGrup as $randGrup1)
               <!-- GRUP 3 -->
                    
              
                  @if(count($groupBy)>=3)
                                  
                <tr>
                 <td>
                  {{$groupBy[2]["type"]==="Date"
                  ?$groupBy[2]["denumire"].": ".dateFormatAfisare(collect($randGrup1->flatten()->values()->all()[0])[$groupBy[2]["col"]])
                  :$groupBy[2]["denumire"].": ".collect($randGrup1->flatten()->values()->all()[0])[$groupBy[2]["col"]]}}
                </td>
               </tr>
               @foreach($randGrup1 as $randGrup2)
                <!-- GRUP 4 -->
                    
             
                  @if(count($groupBy)===4)
                                  
                <tr>
                 <td>
                  {{$groupBy[3]["type"]==="Date"
                   ?$groupBy[3]["denumire"].": ".dateFormatAfisare(collect($randGrup2->flatten()->values()->all()[0])[$groupBy[3]["col"]])
                   :$groupBy[3]["denumire"].": ".collect($randGrup2->flatten()->values()->all()[0])[$groupBy[3]["col"]]}}
                </td>
               </tr>
               @foreach($randGrup2 as $randGrup3)
               <tr>
                @foreach ($antetTabel as $col)
                 <td>
                  @if($col["type"]==="Date")
                   {{dateFormatAfisare(collect($randGrup3)[$col["col"]])}}
                  @else
                   {{collect($randGrup3)[$col["col"]]}}
                  @endif
                 </td>
                @endforeach
               </tr>
               @endforeach
                @if($totalBy)
                  <tr>
                  @foreach ($antetTabel as $col)
                  @if($col["col"]==$groupBy[3]["col"])
                  <td>{{$groupBy[3]["type"]==="Date"
                    ?"TOTAL ".dateFormatAfisare(collect($randGrup2->flatten()->values()->all()[0])[$groupBy[3]["col"]])
                    :"TOTAL ".collect($randGrup2->flatten()->values()->all()[0])[$groupBy[3]["col"]]}}</td>
                  @else
                  <td>
                     @if(in_array($col["col"],$totalBy))  
                       
                       {{round(collect($randGrup2->flatten()->values()->all())->sum($col["col"]),2)}}
                    
                     @endif 
                  </td>
                  @endif
                  @endforeach
                  </tr>
                @endif
              @else
                
               <!-- GRUP 4 -->
               <tr>
                @foreach ($antetTabel as $col)
                 <td>
                   @if($col["type"]==="Date")
                     {{dateFormatAfisare(collect($randGrup2)[$col["col"]])}}
                    @else
                     {{collect($randGrup2)[$col["col"]]}}
                   @endif
                 </td>
                @endforeach
               </tr>
               @endif
               @endforeach
                @if($totalBy)
                  <tr>
                  @foreach ($antetTabel as $col)
                  @if($col["col"]==$groupBy[2]["col"])
                  <td>{{$groupBy[2]["type"]==="Date"
                    ?"TOTAL ".dateFormatAfisare(collect($randGrup1->flatten()->values()->all()[0])[$groupBy[2]["col"]])
                    :"TOTAL ".collect($randGrup1->flatten()->values()->all()[0])[$groupBy[2]["col"]]}}</td>
                  @else
                  <td>
                     @if(in_array($col["col"],$totalBy))  
                       
                       {{round(collect($randGrup1->flatten()->values()->all())->sum($col["col"]),2)}}
                    
                     @endif 
                  </td>
                  @endif
                  @endforeach
                  </tr>
                @endif
              @else
                
               <!-- GRUP 3 -->
               <tr>
                @foreach ($antetTabel as $col)
                 <td>
                   @if($col["type"]==="Date")
                    {{dateFormatAfisare(collect($randGrup1)[$col["col"]])}}
                   @else 
                    {{collect($randGrup1)[$col["col"]]}}
                    @endif
                 </td>
                @endforeach
               </tr>
               @endif
               @endforeach
                @if($totalBy)
                  <tr>
                  @foreach ($antetTabel as $col)
                  @if($col["col"]==$groupBy[1]["col"])
                  <td>{{$groupBy[1]["type"]==="Date"
                    ?"TOTAL ".dateFormatAfisare(collect($randGrup->flatten()->values()->all()[0])[$groupBy[1]["col"]])
                    :"TOTAL ".collect($randGrup->flatten()->values()->all()[0])[$groupBy[1]["col"]]}}</td>
                  @else
                  <td>
                     @if(in_array($col["col"],$totalBy))  
                       
                       {{round(collect($randGrup->flatten()->values()->all())->sum($col["col"]),2)}}
                    
                     @endif 
                  </td>
                  @endif
                  @endforeach
                  </tr>
                @endif
              @else
                <!-- GRUP 2 -->
                <!-- GRUP1 -->
               <tr>  
                @foreach ($antetTabel as $col)
                 <td>
                  @if($col["type"]==="Date")
                   {{dateFormatAfisare(collect($randGrup)[$col["col"]])}}
                  @else
                    {{collect($randGrup)[$col["col"]]}}
                  @endif
                </td>
                @endforeach
               </tr>
               @endif
               @endforeach
                @if($totalBy)
                  <tr>
                  @foreach ($antetTabel as $col)
                  @if($col["col"]==$groupBy[0]["col"])
                  <td>
                    {{$groupBy[0]["type"]==="Date"
                     ?"TOTAL ".dateFormatAfisare(collect($rand->flatten()->values()->all()[0])[$groupBy[0]["col"]])
                     :"TOTAL ".collect($rand->flatten()->values()->all()[0])[$groupBy[0]["col"]]}}
                  </td>
                  @else
                  <td>
                     @if(in_array($col["col"],$totalBy))  
                       
                       {{round(collect($rand->flatten()->values()->all())->sum($col["col"]),2)}}
                    
                     @endif 
                  </td>
                  @endif
                  @endforeach
                  </tr>
                @endif
               
              @else
                          <!-- FARA GRUP -->
               <tr>
                @foreach ($antetTabel as $col)

                 <td>
                 @if($col["type"]==="Date")
                   {{dateFormatAfisare(collect($rand)[$col["col"]])}}
                  @else
                    {{collect($rand)[$col["col"]]}}
                  @endif
                 </td>
               
                @endforeach
               </tr>
             @endif
            @endforeach
     

        <!-- TOTAL GENERAL -->
         @if($totalBy)
            <tr>
            @foreach ($antetTabel as $key=>$col)
             @if($key==0 && !in_array($col["col"],$totalBy))
              <td>
                {{"TOTAL GENERAL "}}
              </td>
             @else 
            <td>
               @if(in_array($col["col"],$totalBy))  
                {{round(collect($tabel->flatten()->values()->all())->sum($col["col"]),2)}}
               @endif 
            </td>
            @endif
            @endforeach
            </tr>
        @endif
        
    </tbody>
</table>

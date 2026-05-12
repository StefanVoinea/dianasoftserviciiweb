@extends('layouts.antet')

@section ('content')
          
<table class="table table-condesed" border="1" style="border-collapse: collapse; " width=100%>
    <thead>
    <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
        <th {{"align=center colspan=".count($antetTabel)." width=100%" }} >
          <center> <h3> {!! nl2br($titluRaport) !!} </h3></center>
          <hr>
         </th> 
    </tr> 
    <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
        @foreach ($antetTabel as $col)
        <th {{"align=center width=".$col["width"]."" }} >
        
            
            {{$col["denumire"]}}
          
        </th>
        @endforeach
    </tr>
    </thead>
    <tbody>
       
            @foreach($tabel as $rand)
             
              @if(count($groupBy)>=1) 
                                  <!-- GRUP 1 -->
                <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                 <td {{"align=left width=".$col["width"]." colspan=".count($antetTabel)  }} > 
                 {{$groupBy[0]["type"]==="Date"
                    ?$groupBy[0]["denumire"].": ".dateFormatAfisare(collect($rand->flatten()->values()->all()[0])[$groupBy[0]["col"]])
                    :$groupBy[0]["denumire"].": ".collect($rand->flatten()->values()->all()[0])[$groupBy[0]["col"]]}}
                 </td>
               </tr>
               @foreach($rand as $randGrup)
                <!-- GRUP 2 -->
                
                  @if(count($groupBy)>=2)                
                <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                 <td {{"align=left width=".$col["width"]." colspan=".count($antetTabel) }}  > 
                   {{$groupBy[1]["type"]==="Date"
                     ?$groupBy[1]["denumire"].": ".dateFormatAfisare(collect($randGrup->flatten()->values()->all()[0])[$groupBy[1]["col"]])
                     :$groupBy[1]["denumire"].": ".collect($randGrup->flatten()->values()->all()[0])[$groupBy[1]["col"]]}}
                 </td>
               </tr>
               @foreach($randGrup as $randGrup1)
               <!-- GRUP 3 -->
                    
              
                  @if(count($groupBy)>=3)
                                  
                <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                 <td {{"align=left width=".$col["width"]." colspan=".count($antetTabel) }} > 
                  {{$groupBy[2]["type"]==="Date"
                  ?$groupBy[2]["denumire"].": ".dateFormatAfisare(collect($randGrup1->flatten()->values()->all()[0])[$groupBy[2]["col"]])
                  :$groupBy[2]["denumire"].": ".collect($randGrup1->flatten()->values()->all()[0])[$groupBy[2]["col"]]}}
                </td>
               </tr>
               @foreach($randGrup1 as $randGrup2)
                <!-- GRUP 4 -->
                    
             
                  @if(count($groupBy)===4)
                                  
                <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                 <td {{"align=left width=".$col["width"]." colspan=".count($antetTabel) }} > 
                   {{$groupBy[3]["type"]==="Date"
                   ?$groupBy[3]["denumire"].": ".dateFormatAfisare(collect($randGrup2->flatten()->values()->all()[0])[$groupBy[3]["col"]])
                   :$groupBy[3]["denumire"].": ".collect($randGrup2->flatten()->values()->all()[0])[$groupBy[3]["col"]]}}
                </td>
               </tr>
               @foreach($randGrup2 as $randGrup3)
               <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                @foreach ($antetTabel as $col)
                 <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                  @if($col["type"]==="Date")
                    @if(str_contains($col["col"],"."))
                    {{dateFormatAfisare(collect($randGrup3)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null)}}
                   @else
                    {{dateFormatAfisare(collect($randGrup3)[$col["col"]]??null)}}
                   @endif 
                   
                  @else

                   @if(str_contains($col["col"],"."))
                    {{collect($randGrup3)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null}}
                   @else
                    {{collect($randGrup3)[$col["col"]]??null}}
                   @endif 
                  @endif
                 </td>
                @endforeach
               </tr>
               @endforeach
                @if($totalBy)
                  <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                   @foreach ($antetTabel as $key=>$col)
                  
                  @if($key==0)
                  <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                   {{$groupBy[3]["type"]==="Date"
                    ?"TOTAL ".dateFormatAfisare(collect($randGrup2->flatten()->values()->all()[0])[$groupBy[3]["col"]])
                    :"TOTAL ".collect($randGrup2->flatten()->values()->all()[0])[$groupBy[3]["col"]]}}</td>
                  @else
                  <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
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
               <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                @foreach ($antetTabel as $col)
                 <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                  @if($col["type"]==="Date")
                    @if(str_contains($col["col"],"."))
                    {{dateFormatAfisare(collect($randGrup2)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null)}}
                   @else
                    {{dateFormatAfisare(collect($randGrup2)[$col["col"]]??null)}}
                   @endif 
                   
                  @else
                   @if(str_contains($col["col"],"."))
                    {{collect($randGrup2)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null}}
                   @else
                    {{collect($randGrup2)[$col["col"]]??null}}
                   @endif 
                  @endif
                 </td>
                @endforeach
               </tr>
               @endif
               @endforeach
                @if($totalBy)
                  <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                  @foreach ($antetTabel as $key=>$col)
                  
                  @if($key==0)
                  <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                 {{$groupBy[2]["type"]==="Date"
                    ?"TOTAL ".dateFormatAfisare(collect($randGrup1->flatten()->values()->all()[0])[$groupBy[2]["col"]])
                    :"TOTAL ".collect($randGrup1->flatten()->values()->all()[0])[$groupBy[2]["col"]]}}</td>
                  @else
                  <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
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
               <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                @foreach ($antetTabel as $col)
                 <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                  @if($col["type"]==="Date")
                    @if(str_contains($col["col"],"."))
                    {{dateFormatAfisare(collect($randGrup1)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null)}}
                   @else
                    {{dateFormatAfisare(collect($randGrup1)[$col["col"]]??null)}}
                   @endif 
                   
                  @else
                   @if(str_contains($col["col"],"."))
                    {{collect($randGrup1)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null}}
                   @else
                    {{collect($randGrup1)[$col["col"]]??null}}
                   @endif 
                  @endif
                 </td>
                @endforeach
               </tr>
               @endif
               @endforeach
                @if($totalBy)
                  <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                  @foreach ($antetTabel as $key=>$col)
                  
                  @if($key==0)
                  <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                  {{$groupBy[1]["type"]==="Date"
                    ?"TOTAL ".dateFormatAfisare(collect($randGrup->flatten()->values()->all()[0])[$groupBy[1]["col"]])
                    :"TOTAL ".collect($randGrup->flatten()->values()->all()[0])[$groupBy[1]["col"]]}}</td>
                  @else
                  <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
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
               <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">  
                @foreach ($antetTabel as $col)
                 <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                   @if($col["type"]==="Date")
                    @if(str_contains($col["col"],"."))
                    {{dateFormatAfisare(collect($randGrup)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null)}}
                   @else
                    {{dateFormatAfisare(collect($randGrup)[$col["col"]]??null)}}
                   @endif 
                   
                  @else
                   @if(str_contains($col["col"],"."))
                    {{collect($randGrup)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null}}
                   @else
                    {{collect($randGrup)[$col["col"]]??null}}
                   @endif 
                  @endif
                </td>
                @endforeach
               </tr>
               @endif
               @endforeach
                @if($totalBy)
                  <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                  @foreach ($antetTabel as $key=>$col)
                  
                  @if($key==0)
                  <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                     {{$groupBy[0]["type"]==="Date"
                     ?"TOTAL ".dateFormatAfisare(collect($rand->flatten()->values()->all()[0])[$groupBy[0]["col"]])
                     :"TOTAL ".collect($rand->flatten()->values()->all()[0])[$groupBy[0]["col"]]}}
                  </td>
                  @else
                  <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
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
               <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                @foreach ($antetTabel as $col)

                 <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                @if($col["type"]==="Date")
                    @if(str_contains($col["col"],"."))
                    {{dateFormatAfisare(collect($rand)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null)}}
                   @else
                    {{dateFormatAfisare(collect($rand)[$col["col"]]??null)}}
                   @endif 
                   
                  @else
                  @if($col["type"]==="Number")
                    @if(str_contains($col["col"],"."))
                    {{number_format(collect($rand)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null,2)}}
                   @else
                    {{number_format(collect($rand)[$col["col"]]??null,2)}}
                   @endif 
                   
                  @else
                     @if(str_contains($col["col"],"."))
                      {{collect($rand)[explode(".",$col["col"])[0]][explode(".",$col["col"])[1]]??null}}
                     @else
                      {{collect($rand)[$col["col"]]??null}}
                     @endif 
                   @endif  
                   @endif
                 </td>
               
                @endforeach
               </tr>
             @endif
            @endforeach
     

        <!-- TOTAL GENERAL -->
         @if($totalBy)
            <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
            @foreach ($antetTabel as $key=>$col)
             @if($key==0 && !in_array($col["col"],$totalBy))
              <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
                {{"TOTAL GENERAL "}}
              </td>
             @else 
            <td {{"align=".$col["align"]." width=".$col["width"]."" }} > 
               @if(in_array($col["col"],$totalBy))  
                {{number_format(round(collect($tabel->flatten()->values()->all())->sum($col["col"]),2),2)}}
               @endif 
            </td>
            @endif
            @endforeach
            </tr>
        @endif
        
    </tbody>
    
</table>
<br><br>
<table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td width="30%" align="center" >
                     CONTABIL SEF,<br><br>
                    {{$company->contabil_sef}}
                   <br><br>
                    ___________________________________
                  </td>
                   <td  width="10%" >
                
              </td>
              <td  width="30%" align="center" >
                   INTOCMIT,<br><br>
                   
                   <br><br>
                    _________________________
                  </td>
                  
                   
                <td  width="15%" >
                
              </td>
          </tr> 
      </table>  
@stop
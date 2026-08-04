
<tr>
    <td class="content-cell" align="left">
    	<table >
       <tr>
       	<td class="content-cell" align="left">
       		<img   src="{{'https://app.dianasoft.ro/images/logo/logo.png'}}" alt="DianaSoft" />
                        
       	</td>
       	<td class="content-cell" style="font-size:12px" align="left">
       		 @if($user)
       		 <b>{{$user->name}}</b> <br>
    			{{$user->functia}}<br>
    	Telefon: {{$user->telefon}}<br>
    	E-mail: {{$user->email}} 
   			@endif
       	</td>	
       </tr>
      </table>
     
      
     
    </td>
    <td class="content-cell" align="left">
    	
    </td>
</tr>


            <tr>
                <td class="footer content-cell" align="left">
                    {{ Illuminate\Mail\Markdown::parse($slot) }}
                </td>
            </tr>
     		<tr>
                <td class="footer content-cell" style="font-size:12px" align="left">
                    Acest e-mail este transmis de Diana Soft SRL, iar conținutul este confidențial, întrucât este adresat numai destinatarului menționat.Conținutul  e-mail-ului poate fi cunoscut și de alte persoane autorizate, în acest scop, de către expeditor și destinatar.Datele cu caracter personal cuprinse în acest e-mail pot fi prelucrate numai în condițiile stabilite de Regulamentul (UE) 2016/679 al Parlamentului European și al Consiliului din 27 aprilie 2016.Totodată precizăm că deschiderea sau reținerea, fără drept, a unei corespondențe adresate altuia, precum și divulgarea fără drept a conținutului unei asemenea corespondențe, chiar atunci când aceasta a fost trimisă, deschisă ori a fost deschisă din greșeală, se supun dispozițiilor legii penale referitoare la violarea secretului corespondenței.În situația în care ați primit corespondența din greșeală, vă rugăm să ne informați despre aceasta imediat pe aceeași cale sau prin alte mijloace de comunicare și apoi să ștergeți definitiv întregul conținut al e-mail-ului.
                </td>
            </tr>

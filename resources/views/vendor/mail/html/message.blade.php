@component('mail::layout')
  

    {{-- Body --}}
    {{ $slot }}
    
    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset
   
    {{-- Footer --}}
    @slot('footer')
    
        @component('mail::footer',['user'=>($user??null)])
             E-mail transmis din aplicatia "DianaSoft"  dezvoltata de Diana Soft SRL
            Telefon: 0744476969 E-mail: office@dianasoft.ro © {{ date('Y') }} All rights reserved.
           
    
        @endcomponent
    @endslot
@endcomponent

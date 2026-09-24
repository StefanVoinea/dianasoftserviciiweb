<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subiectul }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f5f1; font-family:Segoe UI, Helvetica, Arial, sans-serif; color:#2f2f2f;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f6f5f1; padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:620px; background-color:#ffffff; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05);">

                <tr>
                    <td style="padding:26px 30px 6px;">
                        {{-- Textul scris de om, cu locurile goale deja umplute.
                             Randurile lui se pastreaza asa cum le-a scris. --}}
                        <div style="font-size:15px; line-height:1.6; white-space:pre-line;">{{ $textul }}</div>
                    </td>
                </tr>

                {{-- Cele doua butoane, la vedere si mari cat sa se apese si de pe
                     telefon. Apasarea e singurul semn cinstit de interes pe care
                     il putem avea: deschiderile se numara prost, o fapta nu.

                     Cel plin cere demonstratia; cel gol duce la pagina de
                     prezentare, pentru cine vrea sa se uite intai singur, fara
                     sa ceara nimanui nimic. --}}
                <tr>
                    <td style="padding:6px 30px 4px;" align="center">
                        <table role="presentation" cellpadding="0" cellspacing="0" align="center">
                            <tr>
                                <td style="background:#22406f; border-radius:8px;">
                                    <a href="{{ $legaturaDemo }}"
                                       style="display:inline-block; padding:13px 30px; font-size:15px; font-weight:600; color:#ffffff; text-decoration:none;">
                                        Solicită o demonstrație
                                    </a>
                                </td>
                                <td style="width:12px; font-size:0; line-height:0;">&nbsp;</td>
                                <td style="border:1px solid #22406f; border-radius:8px;">
                                    <a href="{{ $legaturaPrezentare }}"
                                       style="display:inline-block; padding:12px 26px; font-size:15px; font-weight:600; color:#22406f; text-decoration:none;">
                                        Prezentarea aplicației
                                    </a>
                                </td>
                            </tr>
                        </table>

                        {{-- Adresa scrisa la vedere: unele programe de posta nu
                             arata unde duce un buton, iar o legatura pe care n-o
                             poti citi inainte s-o apesi nu inspira incredere. --}}
                        <div style="margin-top:10px; font-size:12px; color:#8a8a8a;">
                            sau intrați singur pe
                            <a href="{{ $legaturaPrezentare }}" style="color:#22406f;">{{ preg_replace('#^https?://#', '', $legaturaPrezentare) }}</a>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:22px 30px 26px;">
                        <hr style="border:none; border-top:1px solid #ececec; margin:0 0 14px;">

                        {{-- Cine scrie. O scrisoare de la cineva care nu-si spune
                             numele si adresa n-ar trebui trimisa niciodata. --}}
                        <div style="font-size:12px; line-height:1.6; color:#8a8a8a;">
                            {{ $expeditorNume }}
                            @if ($expeditorAdresa)
                                &middot; {{ $expeditorAdresa }}
                            @endif
                            <br>

                            Ați primit acest mesaj la adresa <strong>{{ $contact->email }}</strong>,
                            aflată în lista publică a firmelor de contabilitate.

                            <br><br>

                            {{-- Dezabonarea, la vedere, nu ascunsa in subsol cu
                                 litera de sase. O apasare si nu mai primiti nimic. --}}
                            <a href="{{ $legaturaDezabonare }}" style="color:#5a5a5a;">
                                Nu doriți să mai primiți mesaje de la noi? Dezabonați-vă aici.
                            </a>
                        </div>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>

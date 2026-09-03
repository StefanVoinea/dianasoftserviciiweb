<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dezabonare</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f5f1; font-family:Segoe UI, Helvetica, Arial, sans-serif; color:#2f2f2f;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:48px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px; background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                <tr>
                    <td style="padding:34px 34px 30px; text-align:center;">

                        @if (! $gasit)
                            <h1 style="margin:0 0 12px; font-size:20px; font-weight:600;">Legătura nu mai este bună</h1>
                            <p style="margin:0; font-size:15px; line-height:1.6; color:#6a6a6a;">
                                Nu am găsit adresa la care duce această legătură. Poate a fost deja
                                folosită, iar adresa scoasă din listă.
                            </p>

                        @elseif ($deAcum)
                            <div style="font-size:40px; line-height:1; margin-bottom:14px;">✓</div>
                            <h1 style="margin:0 0 12px; font-size:20px; font-weight:600;">Gata, v-am scos din listă</h1>
                            <p style="margin:0 0 8px; font-size:15px; line-height:1.6; color:#4a4a4a;">
                                Adresa <strong>{{ $contact->email }}</strong> nu va mai primi mesaje de la noi.
                            </p>
                            <p style="margin:0; font-size:13px; line-height:1.6; color:#8a8a8a;">
                                Nu mai aveți nimic de făcut. Dacă v-ați răzgândit vreodată, scrieți-ne.
                            </p>

                        @else
                            <div style="font-size:40px; line-height:1; margin-bottom:14px;">✓</div>
                            <h1 style="margin:0 0 12px; font-size:20px; font-weight:600;">Erați deja scos din listă</h1>
                            <p style="margin:0; font-size:15px; line-height:1.6; color:#4a4a4a;">
                                Adresa <strong>{{ $contact->email }}</strong> nu mai primește mesaje de la noi
                                din {{ optional($contact->dezabonat_la)->format('d.m.Y') }}.
                            </p>
                        @endif

                        <hr style="border:none; border-top:1px solid #ececec; margin:24px 0 14px;">

                        <div style="font-size:12px; color:#9a9a9a;">
                            {{ config('mail.from.name') }}
                            @if (config('mail.from.address'))
                                &middot; {{ config('mail.from.address') }}
                            @endif
                        </div>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>

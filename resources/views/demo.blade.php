<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitare demonstrație</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f5f1; font-family:Segoe UI, Helvetica, Arial, sans-serif; color:#2f2f2f;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:48px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:540px; background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.06);">
                <tr>
                    <td style="padding:34px 34px 30px;">

                        @if (! $gasit)
                            <h1 style="margin:0 0 12px; font-size:20px; font-weight:600; text-align:center;">Legătura nu mai este bună</h1>
                            <p style="margin:0; font-size:15px; line-height:1.6; color:#6a6a6a; text-align:center;">
                                Nu am găsit firma la care duce această legătură.
                            </p>

                        @elseif ($trimis)
                            <div style="text-align:center;">
                                <div style="font-size:40px; line-height:1; margin-bottom:14px;">✓</div>
                                <h1 style="margin:0 0 12px; font-size:20px; font-weight:600;">Vă mulțumim</h1>
                                <p style="margin:0; font-size:15px; line-height:1.6; color:#4a4a4a;">
                                    Am notat solicitarea și vă contactăm în cel mai scurt timp.
                                </p>
                            </div>

                        @else
                            <h1 style="margin:0 0 8px; font-size:20px; font-weight:600;">Solicitare demonstrație</h1>

                            <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#4a4a4a;">
                                Am notat solicitarea pentru <strong>{{ $contact->denumire }}</strong>.
                                Dacă ne lăsați și un nume și un număr de telefon, știm pe cine să cerem
                                când sunăm. Nu e obligatoriu.
                            </p>

                            <form method="POST" action="{{ url('/demo/' . $contact->jeton) }}">
                                @csrf

                                <label style="display:block; font-size:13px; color:#6a6a6a; margin-bottom:4px;">Persoana de contact</label>
                                <input type="text" name="persoana" maxlength="190"
                                       style="width:100%; box-sizing:border-box; padding:9px 11px; font-size:14px; border:1px solid #dcdcdc; border-radius:7px; margin-bottom:14px;">

                                <label style="display:block; font-size:13px; color:#6a6a6a; margin-bottom:4px;">Telefon</label>
                                <input type="text" name="telefon" maxlength="60"
                                       style="width:100%; box-sizing:border-box; padding:9px 11px; font-size:14px; border:1px solid #dcdcdc; border-radius:7px; margin-bottom:14px;">

                                <label style="display:block; font-size:13px; color:#6a6a6a; margin-bottom:4px;">Ce v-ar interesa (facultativ)</label>
                                <textarea name="mesaj" rows="3" maxlength="1000"
                                          style="width:100%; box-sizing:border-box; padding:9px 11px; font-size:14px; border:1px solid #dcdcdc; border-radius:7px; margin-bottom:18px;"></textarea>

                                <button type="submit"
                                        style="width:100%; padding:11px; font-size:15px; font-weight:600; color:#fff; background:#22406f; border:none; border-radius:8px; cursor:pointer;">
                                    Trimite solicitarea
                                </button>
                            </form>
                        @endif

                        <hr style="border:none; border-top:1px solid #ececec; margin:24px 0 14px;">

                        <div style="font-size:12px; color:#9a9a9a; text-align:center;">
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

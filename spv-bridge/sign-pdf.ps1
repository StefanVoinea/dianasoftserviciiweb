# Semneaza un PDF de declaratie cu certificatul de pe token (Windows cert store),
# replicand semnatura aplicatiei desktop: /Filter Adobe.PPKMS, /SubFilter adbe.pkcs7.sha1,
# camp vizibil pe pagina aleasa, digest SHA-1, CMS ne-detasat prin CSP-ul tokenului.
#
# Pozitia casetei vizibile se poate schimba din parametri: pe formularele ANAF
# zona libera difera de la o declaratie la alta, iar caseta nu trebuie sa cada
# peste continutul tiparit.
param(
    [Parameter(Mandatory = $true)][string]$InPath,
    [Parameter(Mandatory = $true)][string]$OutPath,
    [string]$Thumbprint,
    # Pagina pe care se aseaza caseta: numar, sau 'ultima'
    [string]$Pagina = 'ultima',
    # Coltul din stanga-jos al casetei si dimensiunile ei, in puncte.
    # Implicit: jos in dreapta pe ultima pagina, acolo unde e locul semnaturii
    # pe orice act si unde formularele ANAF au spatiu liber.
    [double]$X = 330,
    [double]$Y = 45,
    [double]$Latime = 235,
    [double]$Inaltime = 78,
    # Motivul semnarii: ajunge in dictionarul semnaturii (il arata Adobe Reader
    # la verificare), nu in caseta de pe pagina.
    [string]$Motiv = 'Semnatura declaratie',
    # Deseneaza doar caseta, fara sa semneze: pentru verificarea asezarii,
    # fara sa fie nevoie de token si de PIN.
    [switch]$DoarAparenta
)

$ErrorActionPreference = 'Stop'

# Fonturile standard nu au diacriticele romanesti; numele de pe certificat se
# scrie fara ele, ca sa nu apara caractere lipsa in caseta.
# Literele se scriu prin coduri, nu direct: scriptul ramane pur ASCII si se
# citeste la fel indiferent de codificarea cu care ajunge pe calculatorul unde
# ruleaza.
function Fara-Diacritice([string]$text) {
    if (-not $text) { return '' }

    $harta = @{
        0x0103 = 'a'; 0x00E2 = 'a'; 0x00EE = 'i'; 0x0219 = 's'; 0x015F = 's'; 0x021B = 't'; 0x0163 = 't'
        0x0102 = 'A'; 0x00C2 = 'A'; 0x00CE = 'I'; 0x0218 = 'S'; 0x015E = 'S'; 0x021A = 'T'; 0x0162 = 'T'
    }

    $rezultat = $text

    foreach ($cod in $harta.Keys) {
        $rezultat = $rezultat.Replace([char]$cod, $harta[$cod])
    }

    return $rezultat
}

# Deseneaza caseta semnaturii: chenar si textul explicativ. Primeste orice
# suprafata de desen iTextSharp (strat de semnatura sau sablon obisnuit).
function Deseneaza-Caseta($panza, [double]$latime, [double]$inaltime, [hashtable]$date) {
    $normal = [iTextSharp.text.pdf.BaseFont]::CreateFont(
        [iTextSharp.text.pdf.BaseFont]::HELVETICA,
        [iTextSharp.text.pdf.BaseFont]::CP1252,
        [iTextSharp.text.pdf.BaseFont]::NOT_EMBEDDED)

    $gros = [iTextSharp.text.pdf.BaseFont]::CreateFont(
        [iTextSharp.text.pdf.BaseFont]::HELVETICA_BOLD,
        [iTextSharp.text.pdf.BaseFont]::CP1252,
        [iTextSharp.text.pdf.BaseFont]::NOT_EMBEDDED)

    $margine = 7.0
    $latimeUtila = $latime - 2 * $margine

    # Fundal alb, ca sa nu se amestece cu eventualul continut de dedesubt
    $panza.SetColorFill([iTextSharp.text.BaseColor]::WHITE)
    $panza.Rectangle(0, 0, $latime, $inaltime)
    $panza.Fill()

    $panza.SetLineWidth(0.8)
    $panza.SetColorStroke((New-Object iTextSharp.text.BaseColor(60, 60, 90)))
    $panza.Rectangle(0.4, 0.4, $latime - 0.8, $inaltime - 0.8)
    $panza.Stroke()

    $y = $inaltime - $margine - 8

    $panza.BeginText()

    $panza.SetColorFill((New-Object iTextSharp.text.BaseColor(60, 60, 90)))
    $panza.SetFontAndSize($gros, 8)
    $panza.SetTextMatrix($margine, $y)
    $panza.ShowText('SEMNAT DIGITAL')

    $y -= 14
    $panza.SetColorFill([iTextSharp.text.BaseColor]::BLACK)
    $panza.SetFontAndSize($gros, 9.5)
    $panza.SetTextMatrix($margine, $y)
    $panza.ShowText((Potriveste (Fara-Diacritice $date['nume']) $gros 9.5 $latimeUtila))

    # Randurile de detaliu, in ordinea in care intereseaza cititorul
    $randuri = @(
        ('Data: ' + $date['data'].ToString('dd.MM.yyyy HH:mm:ss')),
        $(if ($date['serie']) { 'Serie: ' + $date['serie'] } else { $null }),
        $(if ($date['emitent']) { 'Emitent: ' + (Fara-Diacritice $date['emitent']) } else { $null }),
        $(if ($date['expira']) { 'Certificat valabil pana la: ' + $date['expira'] } else { $null })
    )

    $panza.SetFontAndSize($normal, 7)

    foreach ($rand in $randuri) {
        if (-not $rand) { continue }

        $y -= 9.5
        $panza.SetTextMatrix($margine, $y)
        $panza.ShowText((Potriveste $rand $normal 7 $latimeUtila))
    }

    $panza.EndText()
}

# Scurteaza textul cat sa incapa pe latimea casetei; altfel numele lungi de
# emitenti ar iesi din chenar.
function Potriveste([string]$text, $font, [double]$dimensiune, [double]$latimeMax) {
    if (-not $text) { return '' }

    if ($font.GetWidthPoint($text, $dimensiune) -le $latimeMax) { return $text }

    $rezultat = $text

    while ($rezultat.Length -gt 4 -and $font.GetWidthPoint(($rezultat + '...'), $dimensiune) -gt $latimeMax) {
        $rezultat = $rezultat.Substring(0, $rezultat.Length - 1)
    }

    return $rezultat + '...'
}

# Din numele distinctiv al emitentului (CN=..., O=..., C=...) intereseaza doar
# denumirea; restul incarca inutil caseta.
function Extrage-CN([string]$numeDistinctiv) {
    if (-not $numeDistinctiv) { return '' }

    if ($numeDistinctiv -match 'CN=([^,]+)') { return $Matches[1].Trim() }
    if ($numeDistinctiv -match 'O=([^,]+)') { return $Matches[1].Trim() }

    return $numeDistinctiv
}

# Stampileaza caseta in continutul paginii si scrie rezultatul in $iesire.
#
# Fara acest pas, caseta ar exista doar ca aparenta a campului de semnatura,
# adica o adnotare de formular: Adobe Reader o arata, dar vizualizatorul PDF din
# browser nu deseneaza campurile de semnatura, iar pagina apare goala. Desenul
# intra in continutul paginii inainte de semnare, deci ramane acoperit de ea.
function Stampileaza-Caseta($intrare, $iesire, [int]$numarPagina, [hashtable]$date) {
    $reader = New-Object iTextSharp.text.pdf.PdfReader($intrare)
    $fs = [System.IO.File]::Create($iesire)
    $stamper = New-Object iTextSharp.text.pdf.PdfStamper($reader, $fs)

    $panza = $stamper.GetOverContent($numarPagina)
    $sablon = $panza.CreateTemplate($Latime, $Inaltime)
    Deseneaza-Caseta $sablon $Latime $Inaltime $date
    $panza.AddTemplate($sablon, $X, $Y)

    $stamper.Close()
    $reader.Close()
    $fs.Close()
}

try {
    Add-Type -Path (Join-Path $PSScriptRoot 'itextsharp.dll')
    Add-Type -AssemblyName System.Security

    $masura = New-Object iTextSharp.text.pdf.PdfReader($InPath)
    $totalPagini = $masura.NumberOfPages
    $masura.Close()

    $numarPagina = if ($Pagina -eq 'ultima') { $totalPagini } else { [int]$Pagina }
    if ($numarPagina -lt 1) { $numarPagina = 1 }
    if ($numarPagina -gt $totalPagini) { $numarPagina = $totalPagini }

    $acum = Get-Date

    # Certificatul se citeste din magazin si pentru previzualizare, daca s-a dat
    # amprenta: asa se vede asezarea reala, cu numele si seria adevarate.
    $card = $null

    if ($Thumbprint) {
        $store = New-Object System.Security.Cryptography.X509Certificates.X509Store('My', 'CurrentUser')
        $store.Open('ReadOnly')
        $card = $store.Certificates | Where-Object { $_.Thumbprint -eq $Thumbprint }
        $store.Close()

        if (-not $card) { throw "Certificatul cu amprenta $Thumbprint nu a fost gasit in magazin" }
    }

    $dateCaseta = @{
        nume    = if ($card) { Extrage-CN $card.Subject } else { '(previzualizare)' }
        data    = $acum
        serie   = if ($card) { $card.SerialNumber } else { '(serie certificat)' }
        emitent = if ($card) { Extrage-CN $card.Issuer } else { '(emitent certificat)' }
        # Valabilitatea certificatului, nu a semnaturii: dupa aceasta data,
        # titularul nu mai poate semna cu el.
        expira  = if ($card) { $card.NotAfter.ToString('dd.MM.yyyy') } else { '(data expirarii)' }
    }

    # --- doar previzualizarea asezarii, fara semnatura -------------------
    if ($DoarAparenta) {
        Stampileaza-Caseta $InPath $OutPath $numarPagina $dateCaseta

        exit 0
    }

    if (-not $card) { throw 'Lipseste amprenta certificatului' }

    $bcParser = New-Object Org.BouncyCastle.X509.X509CertificateParser
    $chain = New-Object 'Org.BouncyCastle.X509.X509Certificate[]' 1
    $chain[0] = $bcParser.ReadCertificate($card.RawData)

    # Numele din certificat, citit de iTextSharp: acesta ajunge si in dictionarul
    # semnaturii, deci ramane sursa pentru /Name.
    $cn = [iTextSharp.text.pdf.PdfPKCS7]::GetSubjectFields($chain[0]).GetField('CN')

    if ($cn) { $dateCaseta['nume'] = $cn }

    # Intai caseta intra in pagina, apoi se semneaza documentul stampilat.
    $intermediar = [System.IO.Path]::Combine([System.IO.Path]::GetTempPath(), [Guid]::NewGuid().ToString() + '.pdf')
    Stampileaza-Caseta $InPath $intermediar $numarPagina $dateCaseta

    $reader = New-Object iTextSharp.text.pdf.PdfReader($intermediar)
    $fs = [System.IO.File]::Create($OutPath)
    $stamper = [iTextSharp.text.pdf.PdfStamper]::CreateSignature($reader, $fs, [char]0)
    $sap = $stamper.SignatureAppearance

    # Rectangle(llx, lly, urx, ury) — coltul din stanga-jos si cel din dreapta-sus
    $rect = New-Object iTextSharp.text.Rectangle($X, $Y, ($X + $Latime), ($Y + $Inaltime))
    $sap.SetVisibleSignature($rect, $numarPagina, $null)
    $sap.SignDate = $acum
    $sap.SetCrypto($null, $chain, $null, $null)
    $sap.Reason = $Motiv
    $sap.Acro6Layers = $true

    # Aceeasi caseta si ca aparenta a campului de semnatura, ca Adobe Reader sa
    # o arate ca semnatura verificabila, nu doar ca desen pe pagina. Coincide cu
    # cea stampilata, deci nu se vede nicio diferenta.
    Deseneaza-Caseta $sap.GetLayer(2) $Latime $Inaltime $dateCaseta

    $dic = New-Object iTextSharp.text.pdf.PdfSignature([iTextSharp.text.pdf.PdfName]::ADOBE_PPKMS, [iTextSharp.text.pdf.PdfName]::ADBE_PKCS7_SHA1)
    $dic.Date = New-Object iTextSharp.text.pdf.PdfDate($sap.SignDate)
    $dic.Reason = $sap.Reason
    if ($cn) { $dic.Name = $cn }
    $sap.CryptoDictionary = $dic

    $rezervat = 4000
    $exc = New-Object 'System.Collections.Generic.Dictionary[iTextSharp.text.pdf.PdfName,int]'
    $exc.Add([iTextSharp.text.pdf.PdfName]::CONTENTS, [int]($rezervat * 2 + 2))
    $sap.PreClose($exc)

    $sha1 = [System.Security.Cryptography.SHA1]::Create()
    $stream = $sap.GetRangeStream()
    $buffer = New-Object byte[] 8192
    while (($citit = $stream.Read($buffer, 0, $buffer.Length)) -gt 0) {
        [void]$sha1.TransformBlock($buffer, 0, $citit, $buffer, 0)
    }
    [void]$sha1.TransformFinalBlock($buffer, 0, 0)
    $hash = $sha1.Hash

    # CMS ne-detasat peste hash-ul SHA-1 al byte-range-ului; silent=false lasa
    # CSP-ul tokenului sa afiseze dialogul de PIN.
    $contentInfo = New-Object System.Security.Cryptography.Pkcs.ContentInfo(,$hash)
    $signedCms = New-Object System.Security.Cryptography.Pkcs.SignedCms($contentInfo, $false)
    $semnatar = New-Object System.Security.Cryptography.Pkcs.CmsSigner($card)
    $semnatar.IncludeOption = [System.Security.Cryptography.X509Certificates.X509IncludeOption]::EndCertOnly
    $signedCms.ComputeSignature($semnatar, $false)
    $semnatura = $signedCms.Encode()

    if ($semnatura.Length -gt $rezervat) { throw "Semnatura ($($semnatura.Length) octeti) depaseste spatiul rezervat" }

    $completat = New-Object byte[] $rezervat
    [System.Array]::Copy($semnatura, 0, $completat, 0, $semnatura.Length)

    $dic2 = New-Object iTextSharp.text.pdf.PdfDictionary
    $pdfString = New-Object iTextSharp.text.pdf.PdfString(,$completat)
    $dic2.Put([iTextSharp.text.pdf.PdfName]::CONTENTS, $pdfString.SetHexWriting($true))
    $sap.Close($dic2)

    exit 0
} catch {
    [Console]::Error.WriteLine($_.Exception.Message)
    exit 1
} finally {
    if ($intermediar -and (Test-Path $intermediar)) { Remove-Item $intermediar -Force -ErrorAction SilentlyContinue }
}

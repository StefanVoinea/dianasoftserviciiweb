# Uneste mai multe PDF-uri intr-unul singur, pentru tiparire.
#
# Fisierul rezultat NU mai poarta semnaturile digitale ale celor de la care
# vine: la copierea paginilor ele se pierd, fiind legate de octetii documentului
# original. E de asteptat — acesta e un exemplar de tiparit, nu unul de depus.
#
# Fisierul cu lista are pe fiecare rand calea documentului si, dupa un TAB,
# textul de scris in filigran pe paginile lui. Textul poate lipsi.
param(
    [Parameter(Mandatory = $true)][string]$ListPath,
    [Parameter(Mandatory = $true)][string]$OutPath
)

$ErrorActionPreference = 'Stop'

# Fonturile standard nu au diacriticele romanesti; scrise prin coduri, ca
# scriptul sa ramana pur ASCII si sa se citeasca la fel oriunde.
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

try {
    Add-Type -Path (Join-Path $PSScriptRoot 'itextsharp.dll')

    # Caile vin dintr-un fisier, nu din linia de comanda: sunt multe si pot
    # contine spatii, iar linia de comanda are o lungime limitata.
    $randuri = @(Get-Content -LiteralPath $ListPath -Encoding UTF8 | Where-Object { $_.Trim() -ne '' })

    if ($randuri.Count -eq 0) { throw 'Nu s-a primit niciun fisier de unit' }

    # Unirea se face intr-un fisier de lucru: filigranul se scrie dupa aceea,
    # pe documentul deja unit, cand exista texte de scris.
    $areFiligran = $false
    $intermediar = [System.IO.Path]::Combine([System.IO.Path]::GetTempPath(), [Guid]::NewGuid().ToString() + '.pdf')

    $document = New-Object iTextSharp.text.Document
    $fs = [System.IO.File]::Create($intermediar)
    $copiator = New-Object iTextSharp.text.pdf.PdfCopy($document, $fs)

    $document.Open()

    $pagini = 0
    # Pentru fiecare pagina din documentul unit, textul filigranului ei.
    $filigranPePagina = New-Object System.Collections.Generic.List[string]

    foreach ($rand in $randuri) {
        $bucati = $rand -split "`t", 2
        $cale = $bucati[0].Trim()
        $filigran = if ($bucati.Count -gt 1) { (Fara-Diacritice $bucati[1]).Trim() } else { '' }

        if (-not (Test-Path -LiteralPath $cale)) {
            throw "Fisierul $cale nu a fost gasit"
        }

        if ($filigran -ne '') { $areFiligran = $true }

        $reader = New-Object iTextSharp.text.pdf.PdfReader($cale)

        for ($i = 1; $i -le $reader.NumberOfPages; $i++) {
            $copiator.AddPage($copiator.GetImportedPage($reader, $i))
            $filigranPePagina.Add($filigran)
            $pagini++
        }

        $copiator.FreeReader($reader)
        $reader.Close()
    }

    $document.Close()
    $fs.Close()

    if (-not $areFiligran) {
        Move-Item -LiteralPath $intermediar -Destination $OutPath -Force
        Write-Output "pagini=$pagini"

        exit 0
    }

    # A doua trecere: filigranul, pe diagonala, pe fiecare pagina.
    $font = [iTextSharp.text.pdf.BaseFont]::CreateFont(
        [iTextSharp.text.pdf.BaseFont]::HELVETICA_BOLD,
        [iTextSharp.text.pdf.BaseFont]::CP1252,
        [iTextSharp.text.pdf.BaseFont]::NOT_EMBEDDED)

    $reader2 = New-Object iTextSharp.text.pdf.PdfReader($intermediar)
    $fs2 = [System.IO.File]::Create($OutPath)
    $stamper = New-Object iTextSharp.text.pdf.PdfStamper($reader2, $fs2)

    for ($i = 1; $i -le $reader2.NumberOfPages; $i++) {
        $text = $filigranPePagina[$i - 1]

        if (-not $text) { continue }

        $cutie = $reader2.GetPageSizeWithRotation($i)
        $panza = $stamper.GetOverContent($i)

        # Sters, ca sa nu acopere scrisul de dedesubt
        $stare = New-Object iTextSharp.text.pdf.PdfGState
        $stare.FillOpacity = 0.12
        $panza.SaveState()
        $panza.SetGState($stare)

        # Marimea se potriveste latimii paginii, ca textele lungi sa incapa
        $dimensiune = [Math]::Min(52, [Math]::Max(14, ($cutie.Width * 1.15) / [Math]::Max(1, $text.Length)))

        $panza.BeginText()
        $panza.SetFontAndSize($font, $dimensiune)
        $panza.SetColorFill([iTextSharp.text.BaseColor]::GRAY)
        $panza.ShowTextAligned(
            [iTextSharp.text.Element]::ALIGN_CENTER,
            $text,
            $cutie.Width / 2,
            $cutie.Height / 2,
            45)
        $panza.EndText()

        $panza.RestoreState()
    }

    $stamper.Close()
    $reader2.Close()
    $fs2.Close()

    Remove-Item -LiteralPath $intermediar -Force -ErrorAction SilentlyContinue

    Write-Output "pagini=$pagini filigran=da"

    exit 0
} catch {
    [Console]::Error.WriteLine($_.Exception.Message)
    exit 1
}

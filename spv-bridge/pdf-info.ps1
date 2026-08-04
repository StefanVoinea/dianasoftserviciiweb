# Citeste un PDF de declaratie ANAF: daca este semnat si XML-ul atasat in el.
# Declaratiile ANAF au XML-ul original atasat in PDF, deci acesta poate fi
# validat cu DUKIntegrator fara sa fie nevoie de fisierul XML separat.
#
# Cu -Text se scoate si textul scris in pagini. Asa aplicatia afla verdictul unei
# recipise sau randurile vectorului fiscal fara ca documentul sa plece de pe
# calculatorul clientului: pleaca doar ce se citeste din el.
param(
    [Parameter(Mandatory = $true)][string]$Cale,
    [switch]$Text,
    # Peste atatea caractere nu mai are ce citi nimeni; documentele ANAF sunt cu
    # mult sub limita, iar un PDF urias nu are de ce sa umple raspunsul.
    [int]$MaximText = 200000
)

$ErrorActionPreference = 'Stop'

<#
    Raspunsul pleaca spre programul care a chemat scriptul, iar el asteapta
    UTF-8. Fara randul acesta, PowerShell scrie in codul de pagini al consolei:
    diacriticele ies octeti nevalizi, iar JSON-ul nu se mai poate citi deloc.
#>
[Console]::OutputEncoding = New-Object System.Text.UTF8Encoding $false

try {
    Add-Type -Path (Join-Path $PSScriptRoot 'itextsharp.dll')

    $reader = New-Object iTextSharp.text.pdf.PdfReader($Cale)

    # Semnaturile sunt campuri in formularul PDF
    $semnaturi = $reader.AcroFields.GetSignatureNames()
    $semnat = $semnaturi.Count -gt 0

    $semnatari = @()
    foreach ($nume in $semnaturi) {
        $pkcs7 = $reader.AcroFields.VerifySignature($nume)
        $semnatari += [ordered]@{
            camp     = $nume
            semnatar = $pkcs7.SigningCertificate.SubjectDN.ToString()
            integru  = $pkcs7.Verify()
            acoperit = $reader.AcroFields.SignatureCoversWholeDocument($nume)
        }
    }

    # Atasamentele (XML-ul declaratiei, eventual ZIP pentru bilanturi)
    $xmlBase64 = $null
    $numeXml = $null

    $nume_cat = $reader.Catalog.GetAsDict([iTextSharp.text.pdf.PdfName]::NAMES)
    if ($nume_cat) {
        $fisiere = $nume_cat.GetAsDict([iTextSharp.text.pdf.PdfName]::EMBEDDEDFILES)

        if ($fisiere) {
            # Arborele de nume poate fi ierarhic (noduri Kids), deci se citeste
            # cu utilitarul dedicat, nu parcurgand direct tabloul /Names.
            $arbore = [iTextSharp.text.pdf.PdfNameTree]::ReadTree($fisiere)

            # Atasamentul se poate numi „XML”, fara extensie, deci se identifica
            # dupa continut: XML-ul declaratiei incepe cu '<'.
            foreach ($numeFisier in $arbore.Keys) {
                if ($xmlBase64) { break }

                $spec = [iTextSharp.text.pdf.PdfDictionary][iTextSharp.text.pdf.PdfReader]::GetPdfObject($arbore[$numeFisier])
                $ef = $spec.GetAsDict([iTextSharp.text.pdf.PdfName]::EF)
                if (-not $ef) { continue }

                foreach ($cheie in $ef.Keys) {
                    $flux = [iTextSharp.text.pdf.PRStream][iTextSharp.text.pdf.PdfReader]::GetPdfObject($ef.Get($cheie))
                    $continut = [iTextSharp.text.pdf.PdfReader]::GetStreamBytes($flux)

                    if ($continut.Length -eq 0) { continue }

                    $inceput = [System.Text.Encoding]::UTF8.GetString($continut, 0, [Math]::Min(200, $continut.Length)).TrimStart([char]0xFEFF, ' ', "`r", "`n", "`t")

                    if ($inceput.StartsWith('<')) {
                        $numeXml = $numeFisier
                        $xmlBase64 = [Convert]::ToBase64String($continut)
                        break
                    }
                }
            }
        }
    }

    # Textul din pagini, cerut doar cand aplicatia are ce citi din el.
    $textPagini = $null

    if ($Text) {
        $adunat = New-Object System.Text.StringBuilder

        for ($pagina = 1; $pagina -le $reader.NumberOfPages; $pagina++) {
            if ($adunat.Length -ge $MaximText) { break }

            [void]$adunat.AppendLine(
                [iTextSharp.text.pdf.parser.PdfTextExtractor]::GetTextFromPage($reader, $pagina)
            )
        }

        $textPagini = $adunat.ToString()

        if ($textPagini.Length -gt $MaximText) {
            $textPagini = $textPagini.Substring(0, $MaximText)
        }
    }

    $reader.Close()

    [ordered]@{
        semnat     = $semnat
        semnatari  = $semnatari
        nume_xml   = $numeXml
        xml_base64 = $xmlBase64
        text       = $textPagini
    } | ConvertTo-Json -Compress -Depth 4

    exit 0
} catch {
    [Console]::Error.WriteLine($_.Exception.Message)
    exit 1
}

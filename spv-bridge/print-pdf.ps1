# Trimite un PDF la imprimanta, de pe acest calculator.
#
# Windows nu are o comanda proprie de tiparit PDF-uri, asa ca se incearca doua
# cai, in ordinea in care sunt de incredere:
#
#   1. Un program de tiparit dat in bridge.env (IMPRIMARE_EXE) - PDFtoPrinter.exe
#      sau SumatraPDF.exe. E calea sigura: nu depinde de ce program deschide
#      PDF-urile pe calculatorul acela si nu lasa ferestre in urma.
#   2. Verbul PrintTo al Windows-ului, care cere ca PDF-urile sa fie asociate cu
#      un program care stie sa tipareasca (Acrobat Reader, Foxit).
#
# Fara diacritice: PowerShell citeste fisierul ca ANSI daca nu are BOM.

param(
    [Parameter(Mandatory = $true)][string]$Cale,
    [string]$Imprimanta = '',
    [string]$Program = ''
)

$ErrorActionPreference = 'Stop'

if (-not (Test-Path -LiteralPath $Cale)) {
    Write-Output "Fisierul de tiparit nu exista: $Cale"
    exit 1
}

function Asteapta($proces, $secunde) {
    if (-not $proces) { return $true }

    if (-not $proces.WaitForExit($secunde * 1000)) {
        try { $proces.Kill() } catch { }
        return $false
    }

    return $true
}

function ExistaImprimanta($nume) {
    try {
        if (Get-Command Get-Printer -ErrorAction SilentlyContinue) {
            return [bool](Get-Printer -Name $nume -ErrorAction SilentlyContinue)
        }

        return [bool](Get-WmiObject Win32_Printer -Filter "Name='$($nume -replace "'", "''")'" -ErrorAction SilentlyContinue)
    } catch {
        # Daca nu putem verifica, incercam totusi sa tiparim.
        return $true
    }
}

<#
  Documentul e formular XFA dinamic?

  Se recunoaste dupa doua semne puse chiar de cel care l-a facut: "/XFA" in
  formular si "/NeedsRendering true", adica "pagina asta nu e documentul; el se
  deseneaza". Se citeste doar inceputul si sfarsitul fisierului, cat sa nu se
  incarce in memorie declaratii de zeci de megaocteti.
#>
function EsteFormularXfa($cale) {
    try {
        $flux = [System.IO.File]::OpenRead($cale)
        $lungime = $flux.Length
        $cat = [Math]::Min(200000, $lungime)

        $tampon = New-Object byte[] $cat
        $flux.Read($tampon, 0, $cat) | Out-Null
        $inceput = [System.Text.Encoding]::ASCII.GetString($tampon)

        $sfarsit = ''

        if ($lungime -gt $cat) {
            $flux.Seek(-$cat, [System.IO.SeekOrigin]::End) | Out-Null
            $tampon2 = New-Object byte[] $cat
            $flux.Read($tampon2, 0, $cat) | Out-Null
            $sfarsit = [System.Text.Encoding]::ASCII.GetString($tampon2)
        }

        $flux.Close()

        $tot = $inceput + $sfarsit

        return ($tot -match '/NeedsRendering\s*true')
    } catch {
        # Nestiind, se merge pe calea obisnuita: mai bine tiparit decat oprit.
        return $false
    }
}

try {
    <#
      Numele imprimantei se verifica intai. Fara asta, Windows accepta linistit
      un nume gresit: programul asociat porneste, nu tipareste nimic, iar noi am
      raporta "trimis la imprimanta" pentru o hartie care nu iese niciodata.
    #>
    if ($Imprimanta -ne '' -and -not (ExistaImprimanta $Imprimanta)) {
        Write-Output "Imprimanta '$Imprimanta' nu exista pe acest calculator."
        exit 1
    }

    <#
      Declaratiile ANAF sunt formulare XFA: pagina din PDF e doar un loc gol, cu
      scrisul "Please wait...", iar declaratia adevarata se deseneaza abia de
      catre Adobe Reader, din datele XFA. PDFtoPrinter si SumatraPDF nu stiu XFA,
      deci tiparesc chiar acel loc gol — o foaie cu "Please wait..." in loc de
      declaratie.

      Pentru ele se merge deci pe programul asociat PDF-urilor, adica Adobe: e
      singurul care deseneaza formularul. Restul documentelor — recipise,
      decizii, orice PDF obisnuit — raman pe programul dedicat, care e calea
      sigura si nu depinde de ce e instalat pe calculator.
    #>
    $areXfa = EsteFormularXfa $Cale

    if ($areXfa) {
        Write-Output "Documentul este formular XFA; il tiparesc prin programul asociat PDF-urilor (Adobe), singurul care il deseneaza."
    }

    # Calea 1: program dedicat de tiparire
    if (-not $areXfa -and $Program -ne '' -and (Test-Path -LiteralPath $Program)) {
        $nume = [System.IO.Path]::GetFileNameWithoutExtension($Program)

        if ($nume -like 'SumatraPDF*') {
            $argumente = @('-print-to', "`"$Imprimanta`"", '-silent', "`"$Cale`"")

            if ($Imprimanta -eq '') {
                $argumente = @('-print-to-default', '-silent', "`"$Cale`"")
            }
        } else {
            # PDFtoPrinter: <fisier> "<imprimanta>"
            $argumente = @("`"$Cale`"")

            if ($Imprimanta -ne '') {
                $argumente += "`"$Imprimanta`""
            }
        }

        $proces = Start-Process -FilePath $Program -ArgumentList $argumente -PassThru -WindowStyle Hidden

        if (-not (Asteapta $proces 120)) {
            Write-Output "Programul de tiparire nu a raspuns in 120 de secunde."
            exit 1
        }

        Write-Output "Trimis la imprimanta prin $nume."
        exit 0
    }

    # Calea 2: verbul Windows
    if ($Imprimanta -ne '') {
        $proces = Start-Process -FilePath $Cale -Verb PrintTo -ArgumentList "`"$Imprimanta`"" -PassThru -WindowStyle Hidden
    } else {
        $proces = Start-Process -FilePath $Cale -Verb Print -PassThru -WindowStyle Hidden
    }

    # Programul care tipareste ramane deseori deschis; important e ca a pornit.
    Start-Sleep -Seconds 3

    Write-Output "Trimis la imprimanta prin programul asociat PDF-urilor."
    exit 0
} catch {
    Write-Output "Tiparirea a esuat: $($_.Exception.Message)"
    Write-Output "Daca pe acest calculator nu este instalat un program care sa tipareasca PDF-uri, puneti in bridge.env calea catre PDFtoPrinter.exe sau SumatraPDF.exe (IMPRIMARE_EXE)."
    exit 1
}

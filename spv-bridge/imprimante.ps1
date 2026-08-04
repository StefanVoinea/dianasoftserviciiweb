# Imprimantele vazute de acest calculator, pentru bridge.
# Fara diacritice si fara caractere peste ASCII: PowerShell citeste fisierul ca
# ANSI daca nu are BOM, iar textul s-ar strica.

$ErrorActionPreference = 'Stop'

<#
    Raspunsul pleaca spre programul care a chemat scriptul, iar el asteapta
    UTF-8. Fara randul acesta, PowerShell scrie in codul de pagini al consolei:
    diacriticele ies octeti nevalizi, iar JSON-ul nu se mai poate citi deloc.
#>
[Console]::OutputEncoding = New-Object System.Text.UTF8Encoding $false

function Descrie($imprimanta, $implicita) {
    [ordered]@{
        nume      = $imprimanta.Name
        stare     = "$($imprimanta.PrinterStatus)"
        implicita = [bool]$implicita
        locatie   = "$($imprimanta.Location)"
        driver    = "$($imprimanta.DriverName)"
    }
}

try {
    $rezultat = @()

    # Get-Printer exista din Windows 8; pe sisteme mai vechi se cade pe WMI.
    if (Get-Command Get-Printer -ErrorAction SilentlyContinue) {
        $implicita = $null

        try {
            $implicita = (Get-CimInstance Win32_Printer -Filter 'Default = True' -ErrorAction Stop).Name
        } catch {
            $implicita = $null
        }

        $rezultat = @(Get-Printer | ForEach-Object {
            Descrie $_ ($_.Name -eq $implicita)
        })
    } else {
        $rezultat = @(Get-WmiObject Win32_Printer | ForEach-Object {
            Descrie $_ $_.Default
        })
    }

    ConvertTo-Json -InputObject $rezultat -Compress -Depth 3
    exit 0
} catch {
    Write-Output "Eroare la citirea imprimantelor: $($_.Exception.Message)"
    exit 1
}

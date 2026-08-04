# Certificatele din magazinul de certificate Windows, pentru bridge.
# Fara -Thumbprint intoarce toate certificatele disponibile acum (util cand pe
# acelasi calculator se conecteaza succesiv mai multe tokene).
param(
    [string]$Thumbprint
)

$ErrorActionPreference = 'Stop'

<#
    Raspunsul pleaca spre programul care a chemat scriptul, iar el asteapta
    UTF-8. Fara randul acesta, PowerShell scrie in codul de pagini al consolei:
    diacriticele ies octeti nevalizi, iar JSON-ul nu se mai poate citi deloc.
#>
[Console]::OutputEncoding = New-Object System.Text.UTF8Encoding $false

function Descrie($cert) {
    [ordered]@{
        thumbprint      = $cert.Thumbprint
        subiect         = $cert.Subject
        emitent         = $cert.Issuer
        serie           = $cert.SerialNumber
        cn              = $cert.GetNameInfo('SimpleName', $false)
        email           = $cert.GetNameInfo('EmailName', $false)
        valabil_de_la   = $cert.NotBefore.ToString('yyyy-MM-dd HH:mm:ss')
        valabil_pana_la = $cert.NotAfter.ToString('yyyy-MM-dd HH:mm:ss')
        are_cheie       = $cert.HasPrivateKey
    }
}

try {
    $store = New-Object System.Security.Cryptography.X509Certificates.X509Store('My', 'CurrentUser')
    $store.Open('ReadOnly')
    $toate = @($store.Certificates)
    $store.Close()

    if ($Thumbprint) {
        $cert = $toate | Where-Object { $_.Thumbprint -eq $Thumbprint }

        if (-not $cert) {
            throw "Certificatul cu amprenta $Thumbprint nu este disponibil pe acest calculator (tokenul nu este conectat)"
        }

        (Descrie $cert) | ConvertTo-Json -Compress
    } else {
        # Doar certificatele cu cheie privata, valabile azi — adica tokene utilizabile
        $valabile = $toate | Where-Object {
            $_.HasPrivateKey -and $_.NotAfter -gt (Get-Date) -and $_.NotBefore -lt (Get-Date)
        }

        $rezultat = @($valabile | ForEach-Object { Descrie $_ })
        ConvertTo-Json -InputObject $rezultat -Compress -Depth 3
    }

    exit 0
} catch {
    [Console]::Error.WriteLine($_.Exception.Message)
    exit 1
}

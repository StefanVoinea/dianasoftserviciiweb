# Verifica daca cheia de pe token se poate folosi acum, si daca a cerut PIN-ul.
#
# Citirea certificatului din magazinul Windows NU cere PIN: ea atinge doar
# partea publica. PIN-ul se cere abia cand se foloseste cheia privata — la
# semnare, sau la intrarea in SPV cu certificat. De aceea singurul fel de a sti
# daca PIN-ul e deja dat este sa se ceara o semnatura mica: daca driverul il are
# in minte, ea se face pe loc si nimeni nu vede nimic; daca nu-l are, se
# deschide fereastra si omul il scrie.
#
# Asa proba e si declansatorul: nu se poate afla fara sa se forteze, si nu
# trebuie fortat cand nu e nevoie.
param(
    [Parameter(Mandatory = $true)][string]$Thumbprint
)

$ErrorActionPreference = 'Stop'

[Console]::OutputEncoding = New-Object System.Text.UTF8Encoding $false

<#
    Peste cate secunde se socoteste ca omul a fost intrebat.

    O semnare cu PIN-ul deja dat tine sutimi de secunda; una care deschide
    fereastra tine cat ii trebuie omului sa-l scrie. Pragul e pus larg dinadins:
    un token lenes sau un calculator incarcat nu trebuie luat drept fereastra.
#>
$PragSecunde = 2.5

function Raspunde($date) {
    ($date | ConvertTo-Json -Compress)
    exit 0
}

try {
    $store = New-Object System.Security.Cryptography.X509Certificates.X509Store('My', 'CurrentUser')
    $store.Open('ReadOnly')
    $cert = @($store.Certificates) | Where-Object { $_.Thumbprint -eq $Thumbprint }
    $store.Close()

    if (-not $cert) {
        Raspunde ([ordered]@{
            gata    = $false
            cerut   = $false
            motiv   = 'tokenul nu este conectat la acest calculator'
            secunde = 0
        })
    }

    if (-not $cert.HasPrivateKey) {
        Raspunde ([ordered]@{
            gata    = $false
            cerut   = $false
            motiv   = 'certificatul nu are cheie privata pe acest calculator'
            secunde = 0
        })
    }

    $octeti = [System.Text.Encoding]::UTF8.GetBytes('DianaSoft SPV Curier - proba PIN')

    $ceas = [System.Diagnostics.Stopwatch]::StartNew()

    <#
        Cheia se ia prin RSACertificateExtensions, nu prin $cert.PrivateKey:
        tokenele mai noi tin cheia in CNG, iar proprietatea veche intoarce acolo
        nimic. Daca certificatul nu e RSA, se incearca ECDSA.
    #>
    $rsa = [System.Security.Cryptography.X509Certificates.RSACertificateExtensions]::GetRSAPrivateKey($cert)

    if ($rsa) {
        $rsa.SignData(
            $octeti,
            [System.Security.Cryptography.HashAlgorithmName]::SHA256,
            [System.Security.Cryptography.RSASignaturePadding]::Pkcs1
        ) | Out-Null
    } else {
        $ec = [System.Security.Cryptography.X509Certificates.ECDsaCertificateExtensions]::GetECDsaPrivateKey($cert)

        if (-not $ec) {
            Raspunde ([ordered]@{
                gata    = $false
                cerut   = $false
                motiv   = 'cheia de pe token nu se poate folosi (nici RSA, nici ECDSA)'
                secunde = 0
            })
        }

        $ec.SignData($octeti, [System.Security.Cryptography.HashAlgorithmName]::SHA256) | Out-Null
    }

    $ceas.Stop()
    $secunde = [Math]::Round($ceas.Elapsed.TotalSeconds, 2)

    Raspunde ([ordered]@{
        gata    = $true
        cerut   = ($secunde -ge $PragSecunde)
        motiv   = ''
        secunde = $secunde
    })
} catch {
    <#
        Aici ajunge si omul care a inchis fereastra fara sa scrie PIN-ul, si cel
        care l-a gresit de prea multe ori. Se spune ce a raspuns Windows, ca sa
        se deosebeasca un token blocat de unul la care s-a apasat „Renunta".
    #>
    $mesaj = $_.Exception.Message

    if ($_.Exception.InnerException) {
        $mesaj = $mesaj + ' | ' + $_.Exception.InnerException.Message
    }

    Raspunde ([ordered]@{
        gata    = $false
        cerut   = $true
        motiv   = $mesaj
        secunde = 0
    })
}

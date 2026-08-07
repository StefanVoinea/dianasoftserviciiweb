# Diagnoza accesului la certificatul digital, pe calculatorul cu tokenul.
#
# Se verifica, in ordinea in care lucrurile depind unul de altul:
#
#   1. programul local (server.php) si sarcinile programate;
#   2. legatura cu aplicatia (app.dianasoft.ro), pe 443;
#   3. legatura cu ANAF, fara certificat — doar ca sa se vada drumul;
#   4. tokenul: e conectat? certificatul are cheia pe el? mai e valabil?
#   5. semnarea — ea cere cheia de pe token si scoate dialogul de PIN;
#   6. intrarea in SPV cu certificatul, adica tot ce face aplicatia.
#
# La fiecare pas se spune ce a iesit si, cand nu merge, care sunt pricinile
# obisnuite — in ordinea in care merita cautate.
#
# Fara diacritice: PowerShell citeste fisierul ca ANSI daca nu are BOM, iar
# textul s-ar strica. Numele venite de pe certificate se scriu asa cum sunt.

# Cu -FaraSemnare se sar pasii care ating cheia de pe token, deci nu apare
# dialogul de PIN. Se foloseste cand diagnoza se rula de la distanta, fara om in
# fata calculatorului: restul verificarilor merg si asa.
param(
    [switch]$FaraSemnare
)

$ErrorActionPreference = 'Continue'
$ProgressPreference = 'SilentlyContinue'

$dosar = Split-Path -Parent $MyInvocation.MyCommand.Path
$raport = Join-Path $dosar 'diagnoza.txt'
$curl = Join-Path $env:SystemRoot 'System32\curl.exe'

# Se strange totul si in fisier: omul il trimite la asistenta fara sa copieze
# fereastra, iar acolo se citeste tot ce s-a incercat, in ordine.
$randuri = New-Object System.Collections.ArrayList

function Scrie($text, $culoare = 'Gray') {
    Write-Host $text -ForegroundColor $culoare
    [void]$randuri.Add($text)
}

function Titlu($text) {
    Scrie ''
    Scrie ('=== ' + $text + ' ===') 'Cyan'
}

function Bine($text) { Scrie ('  [merge] ' + $text) 'Green' }
function Rau($text) { Scrie ('  [NU MERGE] ' + $text) 'Red' }
function Semn($text) { Scrie ('  [atentie] ' + $text) 'Yellow' }
function Amanunt($text) { Scrie ('       ' + $text) 'DarkGray' }

function Pricini($lista) {
    Scrie '       Pricini obisnuite, in ordinea in care merita cautate:' 'Yellow'
    $i = 1
    foreach ($pricina in $lista) {
        Scrie ('       ' + $i + '. ' + $pricina) 'Yellow'
        $i++
    }
}

<#
    Configurarea programului: de acolo se afla catre cine se vorbeste. Fisierul
    are perechi cheie=valoare, cu randuri de comentariu care incep cu #.
#>
function CitesteEnv($cale) {
    $env = @{}

    if (-not (Test-Path -LiteralPath $cale)) { return $env }

    foreach ($rand in Get-Content -LiteralPath $cale) {
        $curat = $rand.Trim()
        if ($curat -eq '' -or $curat.StartsWith('#') -or -not $curat.Contains('=')) { continue }

        $bucati = $curat.Split('=', 2)
        $env[$bucati[0].Trim()] = $bucati[1].Trim().Trim('"').Trim("'")
    }

    return $env
}

<#
    Cine a semnat certificatul serverului cu care tocmai am vorbit.

    E proba cea mai scurta pentru "traficul e desfacut de antivirus": daca
    emitentul e ESET, Kaspersky, Bitdefender si asa mai departe, atunci nu
    vorbim cu serverul, ci cu programul de pe calculatorul acesta.
#>
function Emitentul($adresa) {
    $cerere = $null

    try {
        $cerere = [Net.HttpWebRequest]::Create($adresa)
        $cerere.Timeout = 15000
        $cerere.Method = 'HEAD'
        $raspuns = $cerere.GetResponse()
        $raspuns.Close()
    } catch {
        # Si un raspuns de eroare inseamna ca strangerea de mana TLS a reusit.
    }

    if ($cerere -and $cerere.ServicePoint -and $cerere.ServicePoint.Certificate) {
        return $cerere.ServicePoint.Certificate.Issuer
    }

    return $null
}

function Desfacut($emitent) {
    if (-not $emitent) { return $false }

    foreach ($nume in @('ESET', 'Kaspersky', 'Bitdefender', 'Avast', 'AVG', 'Norton', 'Symantec',
                        'McAfee', 'Fortinet', 'Sophos', 'Zscaler', 'Netskope', 'Palo Alto', 'Forcepoint')) {
        if ($emitent -match $nume) { return $true }
    }

    return $false
}

<#
    Ca "Cheama", dar aduce si raspunsul.

    La SPV, codul HTTP singur nu dovedeste nimic: fara certificat, lantul ANAF
    trimite spre pagina lui de autentificare, care raspunde tot cu 200. Se
    deosebesc dupa cuprins — serviciul da JSON, pagina de autentificare da HTML.
#>
function CheamaCuCorp($adresa, $optiuni = @()) {
    $fisier = Join-Path $env:TEMP ('diagnoza_corp_' + $PID + '.txt')

    $argumente = @('-sS', '-o', $fisier, '-w', '%{http_code}', '--max-time', '45') + $optiuni + @($adresa)
    $iesire = & $curl @argumente 2>&1
    $cod = $LASTEXITCODE

    $text = ($iesire | Out-String).Trim()
    $status = 0
    if ($text -match '(\d{3})\s*$') { $status = [int]$matches[1] }

    $corp = ''
    if (Test-Path -LiteralPath $fisier) {
        $corp = (Get-Content -LiteralPath $fisier -Raw -ErrorAction SilentlyContinue)
        Remove-Item -LiteralPath $fisier -ErrorAction SilentlyContinue
    }

    if (-not $corp) { $corp = '' }

    return @{ status = $status; cod = $cod; text = $text; corp = $corp.Trim() }
}

<# Raspunsul vine de la serviciul SPV, sau de la pagina de autentificare? #>
function PareRaspunsSpv($corp) {
    if (-not $corp) { return $false }

    $inceput = $corp.TrimStart()

    return $inceput.StartsWith('{') -or $inceput.StartsWith('[')
}

<# Cheama o adresa si intoarce codul HTTP si codul cu care s-a oprit curl. #>
function Cheama($adresa, $optiuni = @()) {
    $argumente = @('-sS', '-o', 'NUL', '-w', '%{http_code}', '--max-time', '45') + $optiuni + @($adresa)
    $iesire = & $curl @argumente 2>&1
    $cod = $LASTEXITCODE

    $text = ($iesire | Out-String).Trim()
    $status = 0
    if ($text -match '(\d{3})\s*$') { $status = [int]$matches[1] }

    return @{ status = $status; cod = $cod; text = $text }
}

function Talcul($cod) {
    switch ([int]$cod) {
        0  { return 'raspuns neasteptat de la server' }
        6  { return 'numele serverului nu poate fi dezlegat (DNS)' }
        7  { return 'legatura nu se poate deschide - port inchis de firewall sau internet cazut' }
        28 { return 'a trecut vremea fara raspuns' }
        35 { return 'strangerea de mana TLS a esuat' }
        52 { return 'serverul a inchis fara sa raspunda' }
        56 { return 'legatura s-a rupt in timp ce se primea raspunsul' }
        58 { return 'certificatul cerut nu s-a gasit in magazinul Windows - tokenul nu e conectat, sau amprenta e alta' }
        60 { return 'certificatul serverului nu este de incredere' }
        default { return ('curl s-a oprit cu codul ' + $cod) }
    }
}

# ---------------------------------------------------------------------------

Scrie ('Diagnoza acces token ANAF - ' + (Get-Date -Format 'yyyy-MM-dd HH:mm:ss')) 'White'
Scrie ('Calculator: ' + $env:COMPUTERNAME + '   Utilizator Windows: ' + $env:USERNAME)
Scrie ('Dosar: ' + $dosar)

$config = CitesteEnv (Join-Path $dosar 'configurare.env')

if ($config.Count -eq 0) {
    Semn 'Nu am gasit configurare.env langa acest script.'
    Amanunt 'Rulati diagnoza din dosarul in care este instalat programul.'
}

$aplicatia = $config['PUNTE_SERVER']
if (-not $aplicatia) { $aplicatia = 'https://app.dianasoft.ro' }

$spv = $config['SPV_BASE_URL']
if (-not $spv) { $spv = 'https://webserviced.anaf.ro/SPVWS2/rest' }

$amprenta = $config['SPV_CERT_THUMBPRINT']
$codAcces = $config['SPV_BRIDGE_TOKEN']
$local = $config['PUNTE_LOCAL']
if (-not $local) { $local = 'http://127.0.0.1:8099' }

# --- 1. Programul local ----------------------------------------------------

Titlu '1. Programul de pe acest calculator'

$sarcini = @(Get-ScheduledTask -TaskName 'Acces token ANAF*' -ErrorAction SilentlyContinue)

if ($sarcini.Count -eq 0) {
    Rau 'Nu exista sarcina programata "Acces token ANAF".'
    Pricini @(
        'programul n-a fost instalat pe acest calculator: rulati instaleaza.bat',
        'sarcina a fost stearsa de altcineva sau de un program de curatare'
    )
} else {
    foreach ($sarcina in $sarcini) {
        Bine ('sarcina "' + $sarcina.TaskName + '" exista, starea: ' + $sarcina.State)
    }
}

$procese = @(Get-Process php -ErrorAction SilentlyContinue)
if ($procese.Count -eq 0) {
    Rau 'Niciun proces php nu ruleaza acum.'
    Pricini @(
        'sarcina programata ruleaza sub alt cont Windows, iar acela nu e conectat acum',
        'antivirusul a oprit php.exe: cautati-l in carantina si adaugati dosarul la excluderi',
        'programul a fost oprit de mana - porniti-l cu porneste-manual.bat'
    )
} else {
    Bine ('ruleaza ' + $procese.Count + ' proces(e) php')
}

$raspunsLocal = Cheama ($local + '/certificate')

if ($raspunsLocal.status -eq 401) {
    Bine ('programul local raspunde pe ' + $local + ' si cere cod de acces (corect)')
} elseif ($raspunsLocal.status -ge 200) {
    Bine ('programul local raspunde pe ' + $local + ' (cod ' + $raspunsLocal.status + ')')
    <#
        Cu codul de acces se vede si daca programul chiar ajunge la certificate.
        Citirea magazinului nu atinge cheia privata, deci nu cere PIN.
    #>
    if ($codAcces) {
        $cuCod = Cheama ($local + '/certificate') @('-H', ('Authorization: Bearer ' + $codAcces))

        if ($cuCod.status -eq 200) {
            Bine 'programul local citeste certificatele de pe acest calculator'
        } else {
            Rau ('programul local nu da lista certificatelor (cod ' + $cuCod.status + ')')
            Pricini @(
                'codul de acces din configurare.env nu e cel cu care a fost facut kitul',
                'tokenul nu e conectat, deci n-are ce lista - vezi pasul 4'
            )
        }
    }
} else {
    Rau ('programul local nu raspunde pe ' + $local + ' - ' + (Talcul $raspunsLocal.cod))
    Pricini @(
        'programul nu ruleaza (vezi mai sus)',
        'ruleaza pe alt port decat cel scris in configurare.env',
        'un alt program tine deja portul: verificati cu "netstat -ano | findstr 8099"'
    )
}

# --- 2. Aplicatia ----------------------------------------------------------

Titlu ('2. Legatura cu aplicatia (' + $aplicatia + ')')

$gazda = ([Uri]$aplicatia).Host

$dns = $null
try { $dns = Resolve-DnsName -Name $gazda -ErrorAction Stop | Select-Object -First 1 } catch { }

if ($dns) {
    Bine ('numele ' + $gazda + ' se dezleaga')
} else {
    Rau ('numele ' + $gazda + ' nu se dezleaga (DNS)')
    Pricini @(
        'calculatorul nu are internet',
        'serverul DNS al retelei nu raspunde',
        'un program de filtrare a retelei opreste dezlegarea numelor'
    )
}

$portul = $false
try { $portul = (Test-NetConnection -ComputerName $gazda -Port 443 -WarningAction SilentlyContinue -InformationLevel Quiet) } catch { }

if ($portul) {
    Bine 'portul 443 se poate deschide'
} else {
    Rau 'portul 443 nu se poate deschide'
    Pricini @(
        'firewall-ul (Windows sau cel din firma) opreste iesirea pe 443',
        'antivirusul opreste legatura inainte sa plece',
        'reteaua cere trecerea printr-un proxy, care nu e configurat'
    )
}

$raspunsApp = Cheama $aplicatia
$emitentApp = Emitentul $aplicatia

if ($raspunsApp.status -ge 200) {
    Bine ('aplicatia raspunde (cod HTTP ' + $raspunsApp.status + ')')
} else {
    Rau ('aplicatia nu raspunde - ' + (Talcul $raspunsApp.cod))
    Amanunt $raspunsApp.text
    Pricini @(
        ('iesirea pe 443 catre ' + $gazda + ' e oprita de firewall sau de antivirus'),
        'traficul criptat e desfacut de antivirus (vezi randul urmator)',
        'aplicatia e in intretinere - se vede intreband-o dintr-un browser'
    )
}

if ($emitentApp) {
    if (Desfacut $emitentApp) {
        Rau ('traficul catre aplicatie este DESFACUT pe drum: ' + $emitentApp)
        Pricini @(
            ('opriti scanarea HTTPS din antivirus pentru ' + $gazda),
            'sau adaugati adresa la excluderile de filtrare SSL/TLS'
        )
    } else {
        Bine ('certificatul aplicatiei vine de la: ' + $emitentApp)
    }
} else {
    Semn 'nu s-a putut citi certificatul aplicatiei'
}

# --- 3. ANAF, fara certificat ---------------------------------------------

Titlu '3. Legatura cu ANAF (fara certificat)'

$gazdaAnaf = ([Uri]$spv).Host
$adresaAnaf = $spv.TrimEnd('/') + '/listaMesaje?zile=1'

$portAnaf = $false
try { $portAnaf = (Test-NetConnection -ComputerName $gazdaAnaf -Port 443 -WarningAction SilentlyContinue -InformationLevel Quiet) } catch { }

if ($portAnaf) {
    Bine ('portul 443 catre ' + $gazdaAnaf + ' se poate deschide')
} else {
    Rau ('portul 443 catre ' + $gazdaAnaf + ' nu se poate deschide')
    Pricini @(
        'firewall-ul opreste iesirea catre ANAF',
        'ANAF are mentenanta - se vede intrand pe www.anaf.ro dintr-un browser'
    )
}

$raspunsAnaf = Cheama $adresaAnaf
$emitentAnaf = Emitentul ('https://' + $gazdaAnaf)

if ($raspunsAnaf.status -ge 200) {
    Bine ('ANAF raspunde (cod HTTP ' + $raspunsAnaf.status + '; fara certificat se asteapta 403)')
} else {
    Rau ('ANAF nu raspunde - ' + (Talcul $raspunsAnaf.cod))
    Amanunt $raspunsAnaf.text
    Pricini @(
        ('iesirea catre ' + $gazdaAnaf + ' e oprita de firewall sau de antivirus'),
        'traficul criptat e desfacut pe drum (vezi randul urmator)',
        'serviciul ANAF e picat - se intampla si asta, se incearca peste un ceas'
    )
}

if ($emitentAnaf) {
    if (Desfacut $emitentAnaf) {
        Rau ('traficul catre ANAF este DESFACUT pe drum: ' + $emitentAnaf)
        Amanunt 'Aici nu e doar o incetineala: cu traficul desfacut, certificatul de pe token'
        Amanunt 'nu mai ajunge la ANAF, iar intrarea in SPV nu are cum sa reuseasca.'
        Pricini @(
            'scoateti adresele ANAF de sub scanarea HTTPS a antivirusului',
            'la ESET: Configurare avansata (F5) - Protectii - SSL/TLS - adrese excluse din filtrare'
        )
    } else {
        Bine ('certificatul ANAF vine de la: ' + $emitentAnaf)
    }
}

# --- 4. Tokenul ------------------------------------------------------------

Titlu '4. Tokenul si certificatul de pe el'

$toate = @(Get-ChildItem Cert:\CurrentUser\My -ErrorAction SilentlyContinue)
$cuCheie = @($toate | Where-Object { $_.HasPrivateKey })

if ($toate.Count -eq 0) {
    Rau 'In magazinul personal al acestui cont Windows nu exista niciun certificat.'
    Pricini @(
        'tokenul nu e conectat in USB',
        'driverul tokenului nu e instalat pe acest calculator',
        'certificatul a fost instalat in alt cont Windows - programul vede numai contul sub care ruleaza',
        'certificatul a fost sters din magazin: se reinstaleaza din driverul tokenului'
    )
} elseif ($cuCheie.Count -eq 0) {
    Rau ('exista ' + $toate.Count + ' certificat(e), dar niciunul cu cheie privata.')
    Pricini @(
        'tokenul nu e conectat: fara el, certificatul ramane fara cheie',
        'driverul tokenului nu ruleaza'
    )
} else {
    Bine ('certificate cu cheie privata: ' + $cuCheie.Count)
}

<#
    Nu se mai alege niciunul.

    Pe acelasi calculator stau adesea doua certificate — unul pentru SPV, altul
    pentru SEAP sau pentru semnat documente. Alegand din burta pe primul, diagnoza
    proba tocmai certificatul nepotrivit si dadea vina pe retea. De acum se
    incearca fiecare, iar la pasul 6 se spune care e primit de ANAF si care nu.
#>
$deIncercat = @()

foreach ($certificat in $cuCheie) {
    $zile = [int]([datetime]$certificat.NotAfter - (Get-Date)).TotalDays
    $stare = 'valabil inca ' + $zile + ' zile'
    if ($zile -lt 0) { $stare = 'EXPIRAT de ' + [Math]::Abs($zile) + ' zile' }
    elseif ($zile -lt 30) { $stare = 'expira in ' + $zile + ' zile' }

    $insemnare = ''
    if ($amprenta -and $certificat.Thumbprint -eq $amprenta.ToUpper()) {
        $insemnare = ' [cel scris in configurare.env]'
    }

    Amanunt ($certificat.Subject + $insemnare)
    Amanunt ('  amprenta ' + $certificat.Thumbprint + ' - ' + $stare)

    if ($zile -ge 0) { $deIncercat += $certificat }
}

if ($amprenta -and -not ($cuCheie | Where-Object { $_.Thumbprint -eq $amprenta.ToUpper() })) {
    Rau ('amprenta din configurare.env (' + $amprenta + ') nu se gaseste printre certificatele de aici')
    Pricini @(
        'tokenul conectat acum e altul decat cel configurat',
        ('certificatul a expirat si a fost inlocuit: stergeti amprenta din configurare.env - ' +
            'aplicatia trimite oricum amprenta ceruta la fiecare operatie')
    )
}

# Cel dintai valabil e folosit doar la proba de semnare, care cere un singur token.
$ales = $deIncercat | Select-Object -First 1

# --- 5. Semnarea -----------------------------------------------------------

Titlu '5. Semnarea (aici tokenul cere codul PIN)'

if (-not [Environment]::UserInteractive) {
    Semn 'Sesiunea aceasta nu are ecran: dialogul de PIN n-ar avea unde sa apara.'
}

if ($FaraSemnare) {
    Semn 'sarita la cerere (-FaraSemnare): nu s-a atins cheia de pe token'
} elseif (-not $ales) {
    Rau 'nu exista certificat cu care sa se incerce semnarea (vezi pasul 4)'
} else {
    Scrie '       Daca apare fereastra de PIN, introduceti-l - asta si probam.' 'Yellow'

    try {
        <#
            Semnarea CMS sta in System.Security, care nu e incarcat din start in
            Windows PowerShell. Fara randul acesta, proba cadea cu 'Cannot find
            type [...Pkcs.ContentInfo]" — adica din vina scriptului, nu a
            tokenului, dar la citit parea ca tokenul nu raspunde.
        #>
        Add-Type -AssemblyName System.Security -ErrorAction Stop

        $continut = [Text.Encoding]::UTF8.GetBytes('proba de semnare ' + (Get-Date -Format 'o'))
        $ci = New-Object System.Security.Cryptography.Pkcs.ContentInfo(,$continut)
        $cms = New-Object System.Security.Cryptography.Pkcs.SignedCms($ci, $false)
        $semnatar = New-Object System.Security.Cryptography.Pkcs.CmsSigner($ales)
        # $false: se lasa CSP-ul tokenului sa arate dialogul de PIN
        $cms.ComputeSignature($semnatar, $false)
        [void]$cms.Encode()

        Bine 'semnarea a reusit: cheia de pe token se poate folosi'
    } catch {
        $motiv = $_.Exception.Message
        Rau ('semnarea a esuat: ' + $motiv)

        if ($motiv -match 'cancel|anulat') {
            Pricini @('dialogul de PIN a fost inchis sau anulat - incercati din nou si introduceti PIN-ul')
        } elseif ($motiv -match 'smart card|Keyset|not found|cheie') {
            Pricini @(
                'tokenul a fost scos din USB',
                'driverul tokenului nu raspunde: scoateti si puneti la loc tokenul',
                'cheia nu mai e pe token - certificatul e doar o copie fara cheie'
            )
        } elseif ($motiv -match 'blocked|blocat|locked') {
            Pricini @('tokenul e blocat de la prea multe PIN-uri gresite: se deblocheaza din driverul lui, cu codul PUK')
        } else {
            Pricini @(
                'dialogul de PIN nu a ajuns sub ochii nimanui: sesiune inchisa de la distanta sau utilizator delogat',
                'porniti "single logon" in driverul tokenului, ca PIN-ul sa fie cerut o data pe sesiune',
                'driverul tokenului nu e instalat complet'
            )
        }
    }
}

# --- 6. SPV cu certificatul ------------------------------------------------

Titlu '6. Intrarea in SPV cu certificatul (ce face aplicatia)'

if ($FaraSemnare) {
    Semn 'sarita la cerere (-FaraSemnare): si apelul acesta cere cheia de pe token'
} elseif ($deIncercat.Count -eq 0) {
    Rau 'nu exista certificat cu care sa se incerce (vezi pasul 4)'
} else {
    $primite = 0

    foreach ($certificat in $deIncercat) {
        Scrie ''
        Scrie ('  Cu certificatul: ' + $certificat.Subject) 'White'

        <#
            Se cheama intocmai ca programul: cu certificatul de pe token, urmand
            redirectarile si tinand prajiturile de sesiune. Fara ele, lantul de
            servere al ANAF raspunde 302 catre pagina lui de autentificare, iar
            proba s-ar incheia cu un raspuns care nu spune nimic.

            Fiecare certificat isi are borcanul lui de prajituri: sesiunea unuia
            n-are ce cauta in proba celuilalt.
        #>
        $prajituri = Join-Path $env:TEMP ('diagnoza_spv_' + $PID + '_' + $certificat.Thumbprint + '.txt')

        $raspuns = CheamaCuCorp $adresaAnaf @(
            # Bara dintre magazin si amprenta se scrie prin interpolare: lipita
            # de mana, se pierde usor la o editare, iar curl raspunde atunci
            # 'Failed to get certificate location" — o eroare care pare a fi a
            # tokenului, desi e a noastra.
            '--cert', "CurrentUser\MY\$($certificat.Thumbprint)",
            '--location',
            '--cookie-jar', $prajituri,
            '--cookie', $prajituri
        )

        Remove-Item -LiteralPath $prajituri -ErrorAction SilentlyContinue

        $inceputul = ''
        if ($raspuns.corp) {
            $inceputul = $raspuns.corp.Substring(0, [Math]::Min(150, $raspuns.corp.Length))
        }

        if ($raspuns.status -eq 200 -and (PareRaspunsSpv $raspuns.corp)) {
            $primite++
            Bine 'SPV a raspuns: acesta e certificatul primit de ANAF'
            Amanunt ('Raspunsul incepe cu: ' + $inceputul)
            Amanunt 'Daca in el scrie "eroare", mesajul e al ANAF si spune ce anume lipseste.'
        } elseif ($raspuns.status -eq 200) {
            Rau 'ANAF a raspuns cu pagina lui de autentificare, nu cu datele din SPV'
            Amanunt ('Raspunsul incepe cu: ' + $inceputul)
        } elseif ($raspuns.status -eq 403 -or $raspuns.status -eq 401) {
            Rau ('ANAF a raspuns ' + $raspuns.status + ': legatura merge, dar certificatul nu e primit')
        } elseif ($raspuns.cod -eq 0) {
            Rau ('ANAF a raspuns ' + $raspuns.status + ', nu ce se astepta')
            Amanunt $raspuns.text
        } else {
            Rau ('apelul nu a reusit - ' + (Talcul $raspuns.cod) + ' (cod HTTP ' + $raspuns.status + ')')
            Amanunt $raspuns.text
        }
    }

    Scrie ''

    if ($primite -eq 0) {
        Rau 'Niciun certificat de pe acest calculator nu a fost primit de ANAF.'
        Pricini @(
            'traficul catre ANAF e desfacut de antivirus: cu el desfacut, niciun certificat nu ajunge intreg (vezi pasul 3)',
            'tokenul cere codul PIN si nu-l introduce nimeni (vezi pasul 5)',
            'niciunul dintre certificate nu e inrolat in SPV pentru vreo firma'
        )
    } else {
        Bine ('Certificate primite de ANAF: ' + $primite + ' din ' + $deIncercat.Count + '.')

        if ($deIncercat.Count -gt 1) {
            Amanunt 'Pe acest calculator stau mai multe certificate. In aplicatie, la SPV -> Certificate'
            Amanunt 'digitale, scoateti din uz pe cele care nu sunt pentru SPV (de pilda cel de SEAP):'
            Amanunt 'asa nu se mai incearca niciodata cu ele.'
        }
    }
}

# ---------------------------------------------------------------------------

Titlu 'Sfarsit'
Scrie 'Cititi de sus in jos: primul pas care nu merge il strica si pe cele de dupa el.' 'White'

Set-Content -LiteralPath $raport -Value $randuri -Encoding UTF8
Scrie ('Raportul s-a scris in ' + $raport) 'White'

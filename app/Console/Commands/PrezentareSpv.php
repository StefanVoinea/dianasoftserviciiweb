<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

/**
 * Documentul de prezentare al modulului SPV Curier, scris intr-un fisier Word.
 *
 * Se face din comanda, nu de mana, ca sa poata fi refacut oricand: la fiecare
 * schimbare a modulului se schimba textul aici si se ruleaza din nou, iar
 * capturile de ecran puse in docs/capturi intra singure la locul lor.
 */
class PrezentareSpv extends Command
{
    protected $signature = 'spv:prezentare
                            {--iesire= : Unde se scrie documentul (implicit docs/SPV-Curier-prezentare.docx)}
                            {--capturi= : Dosarul cu capturi de ecran (implicit docs/capturi)}';

    protected $description = 'Scrie documentul Word de prezentare a modulului SPV Curier';

    /** Cate capturi se asteapta si ce trebuie sa arate fiecare. */
    protected const CAPTURI = [
        'certificate' => 'Fila „Certificate digitale": tokenele citite, calculatorul fiecăruia și starea lor',
        'mesaje' => 'Fila „Mesaje SPV": mesajele aduse de la ANAF, cu filtrele și butonul de descărcare',
        'declaratii' => 'Fila „Declarații fiscale": lotul încărcat, cu starea fiecărei declarații',
        'wizard' => 'Fereastra „SPV Wizard": o eroare de validare explicată, cu linia și coloana din XML',
        'solicitari' => 'Fila „Solicitări ANAF": cererile trimise și răspunsurile primite',
        'entitati' => 'Fila „Entități înrolate": firmele pe care le poate reprezenta certificatul',
        'utilizatori' => 'Fila „Utilizatori": drepturile de semnare și depunere, certificatele atribuite',
        'jurnal' => 'Fila „Jurnal": cine ce a făcut, cu dată și oră',
    ];

    public function handle(): int
    {
        $capturi = $this->option('capturi') ?: base_path('docs/capturi');
        $iesire = $this->option('iesire') ?: base_path('docs/SPV-Curier-prezentare.docx');

        $document = new PhpWord();
        $this->stiluri($document);

        $sectiune = $document->addSection([
            'marginLeft' => 1000, 'marginRight' => 1000,
            'marginTop' => 1000, 'marginBottom' => 1000,
        ]);

        $this->coperta($sectiune);
        $this->ceEste($sectiune);
        $this->puncteForte($sectiune, $capturi);
        $this->modDeUtilizare($sectiune, $capturi);
        $this->laClient($sectiune, $capturi);
        $this->siguranta($sectiune);

        @mkdir(dirname($iesire), 0777, true);
        IOFactory::createWriter($document, 'Word2007')->save($iesire);

        $this->info('Documentul s-a scris în ' . $iesire);
        $this->raportulCapturilor($capturi);

        return 0;
    }

    protected function stiluri(PhpWord $document): void
    {
        $document->setDefaultFontName('Calibri');
        $document->setDefaultFontSize(11);

        $document->addTitleStyle(1, ['size' => 20, 'bold' => true, 'color' => '2E4A7D'], ['spaceAfter' => 200]);
        $document->addTitleStyle(2, ['size' => 14, 'bold' => true, 'color' => '2E4A7D'], ['spaceBefore' => 300, 'spaceAfter' => 120]);
        $document->addTitleStyle(3, ['size' => 12, 'bold' => true], ['spaceBefore' => 200, 'spaceAfter' => 80]);

        $document->addParagraphStyle('normal', ['spaceAfter' => 120, 'lineHeight' => 1.15]);
        $document->addFontStyle('accent', ['bold' => true, 'color' => '2E4A7D']);
        $document->addFontStyle('marunt', ['size' => 9, 'color' => '777777', 'italic' => true]);
    }

    protected function coperta($sectiune): void
    {
        $sectiune->addTextBreak(4);

        $sectiune->addText('SPV Curier', ['size' => 34, 'bold' => true, 'color' => '2E4A7D'], ['alignment' => Jc::CENTER]);
        $sectiune->addText(
            'Declarații, mesaje și solicitări în Spațiul Privat Virtual',
            ['size' => 14, 'color' => '555555'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 400]
        );
        $sectiune->addText(
            'Un modul DianaSoft',
            ['size' => 11, 'color' => '777777'],
            ['alignment' => Jc::CENTER]
        );
        $sectiune->addText(
            'Document de prezentare — ' . now()->format('d.m.Y'),
            ['size' => 10, 'color' => '999999'],
            ['alignment' => Jc::CENTER]
        );

        $sectiune->addPageBreak();
    }

    protected function ceEste($sectiune): void
    {
        $sectiune->addTitle('Ce face SPV Curier', 1);

        $this->paragraf($sectiune, 'SPV Curier duce munca cu Spațiul Privat Virtual de la zeci de apăsări '
            . 'zilnice la câteva. Citește mesajele din SPV, le descarcă și le așază în arhiva firmei; '
            . 'validează declarațiile cu chiar validatorul ANAF, le semnează cu certificatul de pe token '
            . 'și le depune; aduce recipisele și le pune lângă declarația la care răspund.');

        $this->paragraf($sectiune, 'Certificatul digital nu pleacă niciodată de pe token. Programul local, '
            . 'instalat pe calculatorul unde este tokenul, face legătura — iar documentele rămân în arhiva '
            . 'clientului, acolo unde le-a ținut dintotdeauna.');

        $this->paragraf($sectiune, 'Modulul lucrează pentru mai multe firme deodată, cu mai multe tokene, '
            . 'pe calculatoare diferite: fiecare certificat își are calculatorul lui, iar aplicația știe pe '
            . 'care să-l cheme pentru fiecare operație.');
    }

    protected function puncteForte($sectiune, string $capturi): void
    {
        $sectiune->addTitle('Puncte forte', 1);

        $puncte = [
            'Validare înainte de depunere' => 'Fiecare declarație trece prin DUKIntegrator, validatorul '
                . 'ANAF, înainte de a pleca. Declarația respinsă se află înainte de termen, nu după.',
            'D406 validat cu perioada raportată' => 'SAF-T se validează cu anul, luna și tipul perioadei, '
                . 'printr-un lansator propriu. Fără el, validatorul compară declarația cu nomenclatoare '
                . 'vechi și raportează erori care nu există.',
            'Erorile, pe înțelesul oricui' => 'SPV Wizard traduce mesajele validatorului în limba română: '
                . 'ce e greșit, de ce, pe ce linie și coloană din fișierul XML, și chiar rândul acela, cu '
                . 'partea greșită colorată. Interpretările sunt construite pe catalogul de mesaje al '
                . 'validatoarelor ANAF, nu pe ghicit.',
            'Semnare cu tokenul, de la distanță' => 'Semnătura se face pe calculatorul unde este tokenul, '
                . 'prin programul local. Aplicația poate sta oriunde — cheia rămâne pe token, iar dialogul '
                . 'de PIN apare acolo unde trebuie.',
            'Recipisele vin singure' => 'Se aduc automat, la intervalul ales, se așază lângă declarația la '
                . 'care răspund și arată verdictul ANAF în tabel. Confirmarea depunerii nu mai cere căutare '
                . 'manuală în SPV.',
            'Dosarul urmărit' => 'Declarațiile scoase din programul de contabilitate într-un dosar de pe '
                . 'calculatorul clientului sunt luate singure, validate, semnate și — dacă așa s-a bifat — '
                . 'depuse. Ce nu trece de validare nu se pierde: pleacă într-un subdosar, iar oamenii firmei '
                . 'primesc email cu motivul.',
            'Alerte pe tip de document' => 'O somație, o decizie de impunere sau orice alt fel de document '
                . 'ales anunță pe email pe cine trebuie, în clipa în care apare în SPV.',
            'Arhiva rămâne la client' => 'Documentele se scriu direct în dosarul firmei, pe calculatorul '
                . 'clientului, sub structura „Denumire firmă (CUI) / Tip declarație". Nu se schimbă locul '
                . 'unde stau actele.',
            'Tipărire la imprimanta clientului' => 'Declarațiile și recipisele bifate ies pe hârtie chiar '
                . 'acolo, pe imprimanta aleasă pentru fiecare om, cu filigranul firmei. Teancul iese sortat, '
                . 'dintr-o apăsare.',
            'Fără porturi deschise pe router' => 'Pe legătura „prin tunel", programul local întreabă singur '
                . 'serverul ce are de făcut, pe 443, ca orice pagină de internet. Nu se deschide nimic spre '
                . 'internet și nu e nevoie de IP fix.',
            'Drepturi date pe om și pe firmă' => 'Semnarea și depunerea se dau anume, fiindcă semnătura e a '
                . 'persoanei cu tokenul, iar depunerea nu se poate lua înapoi. Fiecare om vede doar '
                . 'certificatele la care are drept.',
            'Jurnal de activitate' => 'Cine, ce, când și cu ce certificat — scris în limbaj obișnuit, ca să '
                . 'poată fi citit fără context tehnic.',
            'Avertizare la expirarea certificatului' => 'Cu zilele alese înainte, pe email. Nu se ajunge în '
                . 'ziua depunerii cu tokenul expirat.',
            'Diagnoză la client' => 'Un singur dublu clic pe calculatorul cu tokenul verifică programul '
                . 'local, legătura cu aplicația, legătura cu ANAF, tokenul, semnarea și intrarea în SPV — și '
                . 'spune, la fiecare pas care nu merge, care sunt pricinile obișnuite.',
        ];

        foreach ($puncte as $titlu => $text) {
            $sectiune->addText($titlu, 'accent', ['spaceBefore' => 160, 'spaceAfter' => 40]);
            $this->paragraf($sectiune, $text);
        }

        $this->captura($sectiune, $capturi, 'certificate');
    }

    protected function modDeUtilizare($sectiune, string $capturi): void
    {
        $sectiune->addPageBreak();
        $sectiune->addTitle('Cum se folosește', 1);

        $pasi = [
            [
                'titlu' => '1. Se citesc tokenele conectate',
                'text' => 'În fila „Certificate digitale" se apasă „Citește token-urile conectate". '
                    . 'Certificatele de pe calculatorul cu tokenul apar în listă, cu titularul, emitentul și '
                    . 'câte zile mai are fiecare. Pentru fiecare certificat se spune ce calculator îl ține, '
                    . 'unde e arhiva firmei și cine are voie să lucreze cu el.',
                'captura' => 'certificate',
            ],
            [
                'titlu' => '2. Se află firmele pe care le poate reprezenta',
                'text' => 'În fila „Entități înrolate", butonul „Inițializează / actualizează lista" '
                    . 'întreabă ANAF pentru ce firme are drepturi certificatul. Lista se poate reîmprospăta '
                    . 'oricând; tot de aici se cer din SPV datele firmelor.',
                'captura' => 'entitati',
            ],
            [
                'titlu' => '3. Se aduc mesajele din SPV',
                'text' => 'În fila „Mesaje SPV" se alege câte zile în urmă se caută și se apasă „Descarcă '
                    . 'mesaje". Mesajele se aduc în loturi, iar documentele se scriu direct în arhiva firmei. '
                    . 'Tot de aici se pun alertele pe email, pe tip de document.',
                'captura' => 'mesaje',
            ],
            [
                'titlu' => '4. Se încarcă declarațiile',
                'text' => 'În fila „Declarații fiscale" se aleg fișierele — XML sau PDF — sau se lasă '
                    . 'dosarul urmărit să le aducă singur. Fiecare declarație se validează cu validatorul '
                    . 'ANAF, iar starea ei se vede în tabel: încărcată, validată, respinsă, semnată, depusă.',
                'captura' => 'declaratii',
            ],
            [
                'titlu' => '5. Se lămuresc erorile de validare',
                'text' => 'La o declarație respinsă, butonul cu fulger deschide SPV Wizard: acolo scrie ce '
                    . 'e greșit, de ce, ce trebuie corectat și unde anume în fișier — linia și coloana, cu '
                    . 'rândul arătat și partea greșită colorată.',
                'captura' => 'wizard',
            ],
            [
                'titlu' => '6. Se semnează și se depune',
                'text' => 'Declarațiile validate se semnează cu certificatul de pe token — dialogul de PIN '
                    . 'apare pe calculatorul cu tokenul — și se depun la ANAF. Se poate bifa ca semnarea și '
                    . 'depunerea să urmeze de la sine după validare.',
                'captura' => null,
            ],
            [
                'titlu' => '7. Se așteaptă recipisele',
                'text' => 'Recipisele se aduc singure, la intervalul bifat, și se așază lângă declarația la '
                    . 'care răspund. Verdictul ANAF se vede în tabel, fără căutare în SPV.',
                'captura' => null,
            ],
            [
                'titlu' => '8. Se cer documente din SPV',
                'text' => 'În fila „Solicitări ANAF" se cer vectorul fiscal, fișa rol, situația sintetică și '
                    . 'celelalte documente. Răspunsurile se caută singure în SPV, se descarcă și se '
                    . 'interpretează, iar rândul solicitării își capătă răspunsul.',
                'captura' => 'solicitari',
            ],
            [
                'titlu' => '9. Se dau drepturile oamenilor',
                'text' => 'În fila „Utilizatori" se face câte un cont pentru fiecare om al firmei, cu '
                    . 'certificatele la care are acces și cu dreptul de a semna sau de a depune. Fiecare vede '
                    . 'doar mesajele certificatelor lui.',
                'captura' => 'utilizatori',
            ],
            [
                'titlu' => '10. Se vede ce s-a lucrat',
                'text' => 'Fila „Jurnal" arată cine ce a făcut, cu dată, oră și certificat — scris în '
                    . 'limbaj obișnuit.',
                'captura' => 'jurnal',
            ],
        ];

        foreach ($pasi as $pas) {
            $sectiune->addTitle($pas['titlu'], 2);
            $this->paragraf($sectiune, $pas['text']);

            if ($pas['captura']) {
                $this->captura($sectiune, $capturi, $pas['captura']);
            }
        }
    }

    protected function laClient($sectiune, string $capturi): void
    {
        $sectiune->addPageBreak();
        $sectiune->addTitle('Ce se instalează la client', 1);

        $this->paragraf($sectiune, 'Pe calculatorul unde stă tokenul se instalează un program mic, adus '
            . 'din aplicație ca un kit: se dezarhivează în C:\\DianaSoft_SPV_Curier și se dă dublu clic pe '
            . '„instaleaza.bat". Programul pornește apoi singur la fiecare autentificare, fără fereastră.');

        $this->paragraf($sectiune, 'Nu e nevoie de PHP instalat, nici de porturi deschise pe router: kitul '
            . 'își aduce PHP-ul de care are nevoie, iar legătura cu aplicația se face „prin tunel" — '
            . 'programul întreabă singur serverul ce are de făcut, pe 443.');

        $this->paragraf($sectiune, 'Când ceva nu merge, tot acolo se află „diagnoza.bat": verifică pe rând '
            . 'programul local, legătura cu aplicația, legătura cu ANAF, tokenul, semnarea și intrarea în '
            . 'SPV cu certificatul, iar la fiecare pas care nu merge spune și pricinile obișnuite. Raportul '
            . 'se scrie într-un fișier, gata de trimis la asistență.');

        $sectiune->addTitle('De știut despre PIN', 3);
        $this->paragraf($sectiune, 'Tokenul își cere codul PIN — așa cere legea, iar certificatul e '
            . 'calificat. Cât de des îl cere ține de driverul tokenului: multe permit „single logon", adică '
            . 'un PIN pe sesiunea Windows, în loc de unul la fiecare semnătură. Nicio aplicație nu poate '
            . 'scăpa cu totul de PIN, iar cine promite asta fie nu știe ce vinde, fie ocolește legea.');
    }

    protected function siguranta($sectiune): void
    {
        $sectiune->addTitle('Siguranță', 1);

        $randuri = [
            'Cheia privată nu părăsește tokenul: toate operațiile care o cer se fac pe calculatorul unde '
                . 'este el conectat.',
            'Fiecare calculator are codul lui de acces, iar comenzile venite de la server sunt semnate; '
                . 'programul local le verifică înainte de a le duce la capăt.',
            'Documentele rămân în arhiva clientului; pe server nu se strânge nimic ce n-are ce căuta acolo.',
            'Accesul în aplicație se poate limita la anumite adrese IP, iar o încercare de la altă adresă '
                . 'anunță pe email.',
            'Fiecare faptă însemnată se scrie în jurnal, cu omul, ora și certificatul folosit.',
        ];

        foreach ($randuri as $rand) {
            $sectiune->addListItem($rand, 0, null, null, ['spaceAfter' => 80]);
        }
    }

    /** Un paragraf obisnuit, cu spatiul lui. */
    protected function paragraf($sectiune, string $text): void
    {
        $sectiune->addText($text, null, 'normal');
    }

    /**
     * Captura de ecran, daca a fost pusa in dosar; altfel locul ei, insemnat.
     *
     * Asa documentul se poate scrie inainte de a exista capturile, iar cand ele
     * apar se ruleaza comanda din nou si intra singure la locul lor.
     */
    protected function captura($sectiune, string $dosar, string $nume): void
    {
        $descriere = self::CAPTURI[$nume] ?? $nume;

        foreach (['png', 'jpg', 'jpeg'] as $extensie) {
            $cale = rtrim($dosar, '/\\') . DIRECTORY_SEPARATOR . $nume . '.' . $extensie;

            if (is_file($cale)) {
                $sectiune->addImage($cale, [
                    'width' => 460,
                    'alignment' => Jc::CENTER,
                    'wrappingStyle' => 'inline',
                ]);
                $sectiune->addText($descriere, 'marunt', ['alignment' => Jc::CENTER, 'spaceAfter' => 200]);

                return;
            }
        }

        $tabel = $sectiune->addTable(['borderSize' => 6, 'borderColor' => 'BBBBBB', 'cellMargin' => 120]);
        $tabel->addRow();
        $celula = $tabel->addCell(9000, ['bgColor' => 'F4F6FA']);
        $celula->addText('[ aici vine captura: ' . $nume . '.png ]', ['bold' => true, 'color' => '2E4A7D'], ['alignment' => Jc::CENTER]);
        $celula->addText($descriere, 'marunt', ['alignment' => Jc::CENTER]);

        $sectiune->addTextBreak(1);
    }

    /** Ce capturi lipsesc, ca sa se stie ce mai e de facut. */
    protected function raportulCapturilor(string $dosar): void
    {
        $lipsa = [];

        foreach (self::CAPTURI as $nume => $descriere) {
            $gasita = false;

            foreach (['png', 'jpg', 'jpeg'] as $extensie) {
                if (is_file(rtrim($dosar, '/\\') . DIRECTORY_SEPARATOR . $nume . '.' . $extensie)) {
                    $gasita = true;
                    break;
                }
            }

            if (!$gasita) {
                $lipsa[$nume] = $descriere;
            }
        }

        if ($lipsa === []) {
            $this->info('Toate capturile sunt la locul lor.');

            return;
        }

        $this->warn('Lipsesc ' . count($lipsa) . ' capturi. Puneți-le în ' . $dosar . ' și rulați din nou:');

        foreach ($lipsa as $nume => $descriere) {
            $this->line('  ' . $nume . '.png — ' . $descriere);
        }
    }
}

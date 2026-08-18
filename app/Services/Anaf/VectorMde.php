<?php

namespace App\Services\Anaf;

use Symfony\Component\Process\Process;

/**
 * Scoate un tabel dintr-un fisier Access (vector.mde, declmf.mde) intr-un CSV.
 *
 * Fisierul e format JET, pe care PHP nu-l citeste singur. Pe Linux se foloseste
 * mdb-export (pachetul mdbtools); pe Windows, driverul ODBC de Access, care e
 * doar pe 32 de biti — de aceea trece printr-un PowerShell pe 32 de biti, nu
 * prin extensia odbc a PHP-ului (pe 64).
 */
class VectorMde
{
    /** Tabelul cautat cand nu se cere altul. Potrivirea nu tine cont de litere. */
    protected const TABEL = 'vectormf';

    /** Tabelul din care se exporta acum. */
    protected $tabel = self::TABEL;

    /**
     * Calea catre un CSV cu tabelul cerut; un CSV primit trece neatins.
     *
     * @throws \RuntimeException cand fisierul nu poate fi citit pe masina asta
     */
    public function inCsv(string $cale, string $tabel = self::TABEL): string
    {
        $this->tabel = $tabel;

        if (strtolower(pathinfo($cale, PATHINFO_EXTENSION)) === 'csv') {
            return $cale;
        }

        if ($this->areMdbTools()) {
            return $this->prinMdbTools($cale);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            return $this->prinOdbc($cale);
        }

        throw new \RuntimeException(
            'Fișierele Access nu pot fi citite pe acest server: lipsește mdbtools. '
            . 'Instalați-l („apt install mdbtools”) sau încărcați tabelul ' . $tabel . ' exportat ca CSV.'
        );
    }

    protected function areMdbTools(): bool
    {
        $unde = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';

        $proces = Process::fromShellCommandline($unde . ' mdb-export');
        $proces->run();

        return $proces->isSuccessful();
    }

    /** Linux: mdb-tables gaseste numele exact al tabelului, mdb-export il varsa. */
    protected function prinMdbTools(string $cale): string
    {
        $tabele = new Process(['mdb-tables', '-1', $cale]);
        $tabele->mustRun();

        $tabel = null;

        foreach (preg_split('/\r?\n/', trim($tabele->getOutput())) as $nume) {
            if (strcasecmp(trim($nume), $this->tabel) === 0) {
                $tabel = trim($nume);

                break;
            }
        }

        if ($tabel === null) {
            throw new \RuntimeException('Fișierul nu are tabelul „' . $this->tabel . '”.');
        }

        $export = new Process(['mdb-export', $cale, $tabel]);
        $export->mustRun();

        $csv = tempnam(sys_get_temp_dir(), 'vmf') . '.csv';
        file_put_contents($csv, $export->getOutput());

        return $csv;
    }

    /** Windows: driverul Access pe 32 de biti, chemat dintr-un PowerShell pe 32. */
    protected function prinOdbc(string $cale): string
    {
        $powershell = getenv('SystemRoot') . '\\SysWOW64\\WindowsPowerShell\\v1.0\\powershell.exe';

        if (!is_file($powershell)) {
            // Windows pe 32 de biti nu are SysWOW64: powershell-ul obisnuit e deja pe 32.
            $powershell = 'powershell.exe';
        }

        $csv = tempnam(sys_get_temp_dir(), 'vmf') . '.csv';

        $script = tempnam(sys_get_temp_dir(), 'vmf') . '.ps1';
        file_put_contents($script, implode("\r\n", [
            'param([string]$Mde, [string]$Csv)',
            '$cs = "Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=$Mde;"',
            '$conn = New-Object System.Data.Odbc.OdbcConnection($cs)',
            '$conn.Open()',
            '$cmd = $conn.CreateCommand()',
            '$cmd.CommandText = "SELECT * FROM ' . $this->tabel . '"',
            '$adapter = New-Object System.Data.Odbc.OdbcDataAdapter($cmd)',
            '$dt = New-Object System.Data.DataTable',
            '[void]$adapter.Fill($dt)',
            '$dt | Export-Csv -Path $Csv -NoTypeInformation -Encoding UTF8',
            '$conn.Close()',
        ]));

        $proces = new Process([
            $powershell, '-NoProfile', '-ExecutionPolicy', 'Bypass',
            '-File', $script, '-Mde', $cale, '-Csv', $csv,
        ]);
        $proces->run();

        @unlink($script);

        if (!$proces->isSuccessful() || !is_file($csv) || filesize($csv) === 0) {
            throw new \RuntimeException(
                'Fișierul Access nu a putut fi citit prin ODBC: '
                . trim($proces->getErrorOutput() ?: $proces->getOutput())
            );
        }

        return $csv;
    }
}

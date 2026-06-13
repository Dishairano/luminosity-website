<?php

namespace App\Services;

use App\Core\Config;

/**
 * Praat met de Python-taal-engine via dezelfde JSON-CLI die de Quickshell-schil
 * gebruikt:  python3 -m luminosity_engine.cli --json "<invoer>"
 *
 * Deze service voert NOOIT zelf iets uit; hij vraagt de engine alleen om de
 * vertaling, uitleg en het veiligheidsoordeel (engine.verwerk). Het echte
 * uitvoeren gebeurt los, in de Sandbox.
 */
final class Engine
{
    private string $python;
    private string $path;

    public function __construct()
    {
        $cfg = Config::get('engine');
        $this->python = $cfg['python'];
        $this->path   = $cfg['path'];
    }

    /**
     * Zet gewone taal om naar een bevestiging (vertaling + veiligheid).
     *
     * @return array{ok:bool,bevestiging?:array,fout?:string}
     */
    public function verwerk(string $invoer): array
    {
        $invoer = trim($invoer);
        if ($invoer === '') {
            return ['ok' => false, 'fout' => 'Geen invoer ontvangen.'];
        }

        $cmd = sprintf(
            'PYTHONPATH=%s %s -m luminosity_engine.cli --json %s 2>/dev/null',
            escapeshellarg($this->path),
            escapeshellarg($this->python),
            escapeshellarg($invoer)
        );

        $out = shell_exec($cmd);
        $data = json_decode((string)$out, true);

        if (!is_array($data) || !isset($data['bevestiging'])) {
            return ['ok' => false, 'fout' => 'De engine gaf geen geldig antwoord.'];
        }

        return ['ok' => true, 'bevestiging' => $data['bevestiging']];
    }
}

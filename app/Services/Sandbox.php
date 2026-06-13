<?php

namespace App\Services;

use App\Core\Config;

/**
 * Voert een commando uit in een wegwerp-Docker-container met strikte limieten.
 *
 * Veiligheidsmaatregelen:
 *   --rm                 container wordt na afloop direct verwijderd
 *   --network none       geen netwerk (geen exfiltratie, geen downloads)
 *   --memory / --cpus    harde resourcelimieten
 *   --pids-limit         geen fork-bombs
 *   --cap-drop ALL       geen Linux-capabilities
 *   --security-opt no-new-privileges
 *   --read-only + tmpfs  alleen /tmp is schrijfbaar, rest is read-only
 *   timeout              de opdracht wordt na X seconden afgebroken
 *
 * Belangrijk: er wordt UITSLUITEND het door de engine goedgekeurde commando
 * doorgegeven (server-side opnieuw bepaald), nooit ruwe invoer van de client.
 */
final class Sandbox
{
    private string $image;
    private int $timeout;

    public function __construct()
    {
        $cfg = Config::get('sandbox');
        $this->image   = $cfg['image'];
        $this->timeout = $cfg['timeout'];
    }

    /**
     * @return array{gelukt:bool,uitvoer:string,exitcode:int}
     */
    public function run(string $commando): array
    {
        // De container draait als root, dus 'sudo' is niet nodig (en niet
        // geïnstalleerd). Strip een leidende sudo zodat het commando authentiek
        // draait in plaats van te falen op "sudo: command not found".
        $commando = preg_replace('/^\s*sudo\s+/', '', $commando);

        $docker = sprintf(
            'docker run --rm --network none --memory 256m --cpus 0.5 '
            . '--pids-limit 128 --cap-drop ALL --security-opt no-new-privileges '
            . '--read-only --tmpfs /tmp:size=64m %s '
            . 'timeout %d bash -lc %s 2>&1',
            escapeshellarg($this->image),
            $this->timeout,
            escapeshellarg($commando)
        );

        $output = [];
        $exit = 0;
        exec($docker, $output, $exit);
        $tekst = trim(implode("\n", $output));

        if ($exit === 124) {
            $tekst = "De opdracht duurde te lang en is in de sandbox afgebroken.";
        } elseif ($tekst === '') {
            $tekst = $exit === 0 ? 'Klaar (geen uitvoer).' : 'Geen uitvoer.';
        }

        return [
            'gelukt'   => $exit === 0,
            'uitvoer'  => $tekst,
            'exitcode' => $exit,
        ];
    }
}

<?php

namespace App\Services\Anaf\Spv;

/**
 * Programul local de pe calculatorul clientului nu cunoaste inca operatia
 * ceruta — e dintr-un kit mai vechi decat aplicatia.
 *
 * Nu e o eroare a omului si nu se arata nimanui: cine o prinde face lucrarea pe
 * drumul dinainte, ca sa mearga si instalarile neactualizate.
 */
class ProgramLocalVechiException extends SpvException
{
}

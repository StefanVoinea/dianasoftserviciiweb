<?php

namespace App\Services\Anaf\Spv;

/**
 * Cererea a plecat spre ANAF, dar raspunsul nu a mai ajuns: legatura s-a rupt
 * in timp ce se primea (curl 56 / SEC_E_CONTEXT_EXPIRED).
 *
 * Nu e o cerere picata: ANAF a inscris-o aproape sigur, iar raspunsul ei va
 * aparea in SPV ca oricare altul. Se deosebeste de celelalte erori ca sa nu
 * fie trimisa inca o data (clientii primeau cate 2-3 fise rol la o apasare)
 * si ca sa i se scrie totusi randul, fara numarul de solicitare - acela se
 * afla cand raspunsul e citit din SPV.
 */
class CerereNeconfirmataException extends SpvException
{
}

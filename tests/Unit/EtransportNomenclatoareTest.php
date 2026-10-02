<?php

namespace Tests\Unit;

use App\Services\Anaf\Etransport\Nomenclatoare;
use Tests\TestCase;

/**
 * Scopul cu care pornesc liniile declarației e-Transport.
 *
 * La retururi (livrare intracomunitară) omul schimba „Comercializare" în
 * „Altele" linie cu linie; acum pornesc de-a dreptul pe „Altele".
 */
class EtransportNomenclatoareTest extends TestCase
{
    public function test_la_livrarea_intracomunitara_scopul_implicit_e_altele()
    {
        $this->assertSame(9901, Nomenclatoare::scopImplicit(20));
    }

    public function test_la_celelalte_operatiuni_scopul_implicit_e_primul_permis()
    {
        $this->assertSame(101, Nomenclatoare::scopImplicit(10));
        $this->assertSame(101, Nomenclatoare::scopImplicit(30));
        $this->assertSame(9999, Nomenclatoare::scopImplicit(50));
    }

    /** Scopul implicit e mereu unul pe care ANAF îl primește la operațiunea aceea. */
    public function test_scopul_implicit_e_printre_cele_permise()
    {
        foreach (array_keys(Nomenclatoare::TIPURI_OPERATIUNE) as $tip) {
            $this->assertContains(
                Nomenclatoare::scopImplicit($tip),
                Nomenclatoare::SCOPURI_PE_OPERATIUNE[$tip],
                'operațiunea ' . $tip
            );
        }
    }
}

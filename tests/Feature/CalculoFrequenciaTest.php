<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CalculoFrequenciaTest extends TestCase
{
    public function test_calcula_porcentagem_corretamente(): void
    {
        $aulasDadas = 20;
        $faltas = 5;

        $presencas = $aulasDadas - $faltas;
        $porcentagem = ($presencas / $aulasDadas) * 100;

        $this->assertEquals(75, $porcentagem);
    }
}
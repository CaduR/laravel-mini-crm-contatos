<?php

namespace Tests\Unit\Domain\Contacts;

use App\Domain\Contacts\Services\CalculateContactScoreService;
use PHPUnit\Framework\TestCase;

class CalculateContactScoreTest extends TestCase
{
    public function test_calculate_score(): void
    {
        $service = new CalculateContactScoreService;

        // 2 palavras no nome = 10
        // email corporaivo ganha 20 pontos. terminados em .br ganha 10 pontos
        // ddd's de sp 11 à 19 +20 pontos, senão +10 pontos
        $score = $service->calculate('Cadu Rodrigues', 'test@empresa.com.br', '11999999999');

        $this->assertEquals(60, $score);
    }
}

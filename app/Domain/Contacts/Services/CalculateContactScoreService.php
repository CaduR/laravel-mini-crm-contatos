<?php

namespace App\Domain\Contacts\Services;

class CalculateContactScoreService
{
    public function calculate(string $name, string $email, string $phone): int
    {
        $score = 0;
        // Transforma o nome em uma lista
        $palavras = explode(' ', trim($name));
        // Se tiver mais de 1 item soma 10
        if (count($palavras) > 1) {
            $score += 10;
        }

        // Pega os dois primeiros numeros do contato
        $ddd = (int) substr($phone, 0, 2);
        // Se estiver entre 11 e 19 ganha 20 pontos, senão ganha 10 pontos
        if ($ddd >= 11 && $ddd <= 19) {
            $score += 20;
        } else {
            $score += 10;
        }

        $dominiosPadrao = ['gmail.com', 'hotmail.com', 'outlook.com', 'yahoo.com'];
        // Pega depois do @
        $parteEmail = explode('@', $email);
        $dominio = $parteEmail[1] ?? '';

        // Se o dominio nao for padrao, ganha 20 pontos (corporativo)
        if (! in_array($dominio, $dominiosPadrao)) {
            $score += 20;
        }

        // e-mails com .br no final
        if (str_ends_with($email, '.br')) {
            $score += 10;
        }

        return $score;
    }
}

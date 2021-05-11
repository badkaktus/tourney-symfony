<?php

declare(strict_types=1);

namespace App\Tests\Tools;

use App\Tools\Results;
use PHPUnit\Framework\TestCase;

class ResultsTest extends TestCase
{

    public function testRoundResults()
    {
    }

    public function testGetRandScore()
    {
        $score = Results::getRandScore();
        self::assertLessThan(9, $score, 'Снегерированное значение больше ожидаемого');
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Tools;

use App\Tools\Results;
use PHPUnit\Framework\TestCase;

class ResultsTest extends TestCase
{

    /**
     * @dataProvider providerSimpleData
     */
    public function testRoundResults($simpleData)
    {
        exit(var_dump($simpleData));
        $playoffData = [];
        $ret = Results::roundResults($simpleData, 4, $playoffData);

        self::assertIsArray($ret);
        self::assertCount(3, $ret);
        self::assertArrayHasKey(4, $ret);
        self::assertIsArray($ret[4]);
        self::assertCount(4, $ret[4]);
        self::assertIsArray($ret[2]);
        self::assertCount(2, $ret[2]);
        self::assertIsArray($ret[1]);
        self::assertCount(1, $ret[1]);

        self::assertArrayHasKey('winnerId', $ret[1][0]);
        self::assertIsInt($ret[1][0]['winnerId']);
//        exit(var_dump($ret));
    }

    public function testGetRandScore()
    {
        $score = Results::getRandScore();
        self::assertLessThan(9, $score, 'Снегерированное значение больше ожидаемого');
    }

    public function providerSimpleData()
    {
        return [
            'first' => [[
                [
                    'teamId' => 1,
                    'teamName' => 'Riga FC'
                ],
                [
                    'teamId' => 2,
                    'teamName' => 'RFS'
                ],
                [
                    'teamId' => 3,
                    'teamName' => 'Valmiera FC'
                ],
                [
                    'teamId' => 4,
                    'teamName' => 'BFC Daugavpils'
                ],
                [
                    'teamId' => 5,
                    'teamName' => 'FK Liepāja'
                ],
                [
                    'teamId' => 6,
                    'teamName' => 'FK Metta'
                ],
                [
                    'teamId' => 7,
                    'teamName' => 'FK Spartaks'
                ],
                [
                    'teamId' => 8,
                    'teamName' => 'FK Ventspils'
                ],
            ]],
//            [
//                [
//                    'teamId' => 102,
//                    'teamName' => 'Riga FC'
//                ],
//                [
//                    'teamId' => '2',
//                    'teamName' => 'RFS'
//                ],
//                [
//                    'teamId' => 32,
//                    'teamName' => 'Valmiera FC'
//                ],
//                [
//                    'teamId' => 441,
//                    'teamName' => 'BFC Daugavpils'
//                ],
//                [
//                    'teamId' => 5111,
//                    'teamName' => 'FK Liepāja'
//                ],
//                [
//                    'teamId' => 006,
//                    'teamName' => 'FK Metta'
//                ],
//                [
//                    'teamId' => 'dddd',
//                    'teamName' => 'FK Spartaks'
//                ],
//                [
//                    'teamId' => 9182,
//                    'teamName' => 'FK Ventspils'
//                ],
//            ],
        ];
    }
}

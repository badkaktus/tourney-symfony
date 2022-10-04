<?php

declare(strict_types=1);

namespace App\Tools;

use Exception;

class Results
{
    /**
     * Возвращает случайное количество забитых голов
     *
     * @throws Exception
     */
    public static function getRandScore(): int
    {
        return random_int(0, 8);
    }

    /**
     * Рекурсивная функция для генерации результатов плей-офф
     *
     * @throws Exception
     */
    public static function roundResults(array $teams, int $round, array &$res): array
    {
        $teamsInNextRound = [];
        $y = 0;

        for ($i = 0; $i < $round; $i++) {
            do {
                $scoreFirstTeam = self::getRandScore();
                $scoreSecondTeam = self::getRandScore();
            } while (
                $scoreFirstTeam === $scoreSecondTeam
            );
            $res[$round][$i] = [
                'firstTeamScore' => $scoreFirstTeam,
                'firstTeam' => $teams[$y]['teamId']
            ];
            if ($scoreFirstTeam > $scoreSecondTeam) {
                $res[$round][$i]['winnerId'] = $teams[$y]['teamId'];
                $teamsInNextRound[] = $teams[$y];
            }
            $y++;

            $res[$round][$i]['secondTeamScore'] = $scoreSecondTeam;
            $res[$round][$i]['secondTeam'] = $teams[$y]['teamId'];
            if ($scoreFirstTeam < $scoreSecondTeam) {
                $res[$round][$i]['winnerId'] = $teams[$y]['teamId'];
                $teamsInNextRound[] = $teams[$y];
            }

            $y++;
        }

        if ($round > 1) {
//            $res[$round]['afterRoundTeams'] = $teams;
            self::roundResults($teamsInNextRound, $round / 2, $res);
        }

        return $res;
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Matches;
use Doctrine\ORM\EntityManagerInterface;

class MatchesService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Получаем и распределяем сыгранные матчи в турнире
     *
     * @param int $id
     * @return array
     */
    public function tourneyResults(int $id): array
    {
        // получаем результаты игр
        $results = $this->entityManager->getRepository(Matches::class)->findBy(
            [
                'tourneyId' => $id
            ]
        );

        $return = [];

        foreach ($results as $result) {
            $teamHome = $result->getTeamHome();
            $teamAway = $result->getTeamAway();

            if ($result->getRound() === 'g') {
                $key = $result->getGroupLetter();
                $return[$key][$teamHome->getId()][$teamAway->getId()]
                    = $result->getScoreHome() . ':' . $result->getScoreAway();
                $return[$key][$teamAway->getId()][$teamHome->getId()]
                    = $result->getScoreAway() . ':' . $result->getScoreHome();
            }

            if ($result->getRound() === 'p') {
                $key = $result->getPlayOffRound();
                $return[$key][] = [
                    'firstTeam' => $teamHome->getName(), //$result->getTeamHome(),
                    'firstTeamScore' => $result->getScoreHome(),
                    'secondTeam' => $teamAway->getName(),
                    'secondTeamScore' => $result->getScoreAway(),
                ];
            }
        }

        return $return;
    }
}

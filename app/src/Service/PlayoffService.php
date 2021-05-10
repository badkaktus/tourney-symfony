<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Command;
use App\Entity\Groups;
use App\Entity\Matches;
use Doctrine\ORM\EntityManagerInterface;
use Tools\Results;
use Doctrine\DBAL\Exception\InvalidArgumentException;

class PlayoffService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * получаем первые 4 команды в группе
     *
     * @param int $tourneyId
     * @param string $groupLetter
     * @return array
     */
    public function firstFourCommandsFromGroup(int $tourneyId, string $groupLetter): array
    {
        $commandsInGroup = $this->entityManager->getRepository(Groups::class)->findBy(
            criteria: [
                          'tourneyId' => $tourneyId,
                          'groupLetter' => $groupLetter
                      ],
            orderBy: ['points' => 'DESC'],
            limit: 4
        );

        $groupPlayOff = [];
        foreach ($commandsInGroup as $item) {
            $groupPlayOff[] = [
                'teamId' => $item->getTeamId(),
                'points' => (int)$item->getPoints()
            ];
        }

        return $groupPlayOff;
    }

    /**
     * @param int $tourneyId
     * @return bool
     * @throws \Exception
     */
    public function generateResults(int $tourneyId): bool
    {
        $groupAPlayOff = $this->firstFourCommandsFromGroup($tourneyId, 'a');
        $groupBPlayOff = $this->firstFourCommandsFromGroup($tourneyId, 'b');

        if (count($groupAPlayOff) !== 4 || count($groupBPlayOff) !== 4) {
            throw new InvalidArgumentException('нехватает команд для генерации плей-офф');
        }

        // объединяем команды для стартовой сетки
        $allCommandsInPO = [];
        $y = 3;
        for ($i = 0; $i < 4; $i++) {
            $allCommandsInPO[] = $groupAPlayOff[$i];
            $allCommandsInPO[] = $groupBPlayOff[$y];
            $y--;
        }

        $playoffData = [];
        $ret = Results::roundResults($allCommandsInPO, 4, $playoffData);

        // записываем
        foreach ($ret as $round => $itemRound) {
            foreach ($itemRound as $i => $item) {
                // находим entity для домашней команды
                $commandHome = $this->entityManager
                    ->getRepository(Command::class)
                    ->find($item['firstTeam']);
                // находим entity для гостевой команды
                $commandAway = $this->entityManager
                    ->getRepository(Command::class)
                    ->find($item['secondTeam']);

                // записываем матч
                $match = new Matches();
                $match
                    ->setTourneyId($tourneyId)
                    ->setRound('p')
                    ->setTeamHome($commandHome)
                    ->setTeamAway($commandAway)
                    ->setScoreHome($item['firstTeamScore'])
                    ->setScoreAway($item['secondTeamScore'])
                    ->setPlayOffRound($round);
                $this->entityManager->persist($match);
            }
        }

        $this->entityManager->flush();

        return true;
    }
}

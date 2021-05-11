<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Command;
use App\Entity\Groups;
use App\Entity\Matches;
use App\Entity\Tourney;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use App\Tools\Results;

class GroupsService
{
    private const TEAM_LIMIT = 8;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Распределяем команды по группам и записываем в БД
     *
     * @param array $commands
     * @param int $tourneyId
     * @return bool
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function drawToGroups(array $commands, int $tourneyId): bool
    {
        $groups = [
            'a' => [],
            'b' => []
        ];

        $conn = $this->entityManager->getConnection();

        $groupList = ['a', 'b'];
        foreach ($commands as $command) {
            $randomGroupId = random_int(0, 1);
            if (count($groups[$groupList[$randomGroupId]]) >= self::TEAM_LIMIT) {
                $randomGroupId = ($randomGroupId === 0) ? 1 : 0;
            }
            $groups[$groupList[$randomGroupId]][] = $command->getName();

            $sql = <<<SQL
INSERT INTO `groups` SET tourney_id = :tourney_id, group_letter = :letter, team_id = :team_id
SQL;

            $stmt = $conn->prepare($sql);
            $stmt->executeQuery(
                [
                    'tourney_id' => $tourneyId,
                    'letter' => $groupList[$randomGroupId],
                    'team_id' => $command->getId(),
                ]
            );
        }

        return true;
    }

    /**
     * Получаем команды в турнире
     *
     * @param int $tourneyId
     * @return array|array[]
     */
    #[ArrayShape(['a' => "array", 'b' => "array"])]
    public function commandsInTourney(
        int $tourneyId
    ): array {
        $teamsByGroups = ['a' => [], 'b' => []];

        $commandsInTourney = $this->entityManager->getRepository(Groups::class)->findBy(
            [
                'tourneyId' => $tourneyId
            ],
        );

        foreach ($commandsInTourney as $item) {
            $teamsByGroups[$item->getGroupLetter()][$item->getTeamId()] = $item->getPoints();
        }

        return $teamsByGroups;
    }

    /**
     * Возвращаем команды в группе/турнире
     *
     * @param int $tourneyId
     * @param string $groupLetter
     * @return array
     */
    private function commandsInGroup(int $tourneyId, string $groupLetter): array
    {
        return $this->entityManager->getRepository(Groups::class)->findBy(
            [
                'tourneyId' => $tourneyId,
                'groupLetter' => $groupLetter
            ]
        );
    }

    /**
     * Генерация результатов в группе
     *
     * @param int $tourney
     * @param string $group
     * @return bool
     * @throws \Exception
     */
    public function generateResults(int $tourneyId, string $groupLetter): bool
    {
        $commandsInGroup = $this->commandsInGroup($tourneyId, $groupLetter);

        $commands = [];
        $commandModels = [];
        foreach ($commandsInGroup as $item) {
            $commands[] = $item->getTeamId();
            $commandModels[$item->getTeamId()] = $item;
        }
        $cntCommand = count($commands);

        foreach ($commands as $i => $teamHome) {
            if (isset($commands[$i + 1])) {
                for ($y = $i + 1; $y < $cntCommand; $y++) {
                    if ($teamHome === $commands[$y]) {
                        continue;
                    }
                    $scoreHome = Results::getRandScore();
                    $scoreAway = Results::getRandScore();
                    switch ($scoreHome <=> $scoreAway) {
                        case -1:
                            // $scoreHome < $scoreAway
                            $commandModels[$commands[$y]]->setPoints(
                                $commandModels[$commands[$y]]->getPoints() + 3
                            );
                            break;

                        case 1:
                            // $scoreHome > $scoreAway
                            $commandModels[$teamHome]->setPoints(
                                $commandModels[$teamHome]->getPoints() + 3
                            );
                            break;

                        case 0:
                            // =
                            $commandModels[$commands[$y]]->setPoints(
                                $commandModels[$commands[$y]]->getPoints() + 1
                            );
                            $commandModels[$teamHome]->setPoints(
                                $commandModels[$teamHome]->getPoints() + 1
                            );
                            break;
                    }

                    // находим entity для домашней команды
                    $commandHome = $this->entityManager
                        ->getRepository(Command::class)
                        ->find($teamHome);

                    // находим entity для гостевой команды
                    $commandAway = $this->entityManager
                        ->getRepository(Command::class)
                        ->find($commands[$y]);

                    // записываем матч
                    $match = new Matches();
                    $match
                        ->setRound('g')
                        ->setGroupLetter($groupLetter)
                        ->setTourneyId($tourneyId)
                        ->setScoreHome($scoreHome)
                        ->setScoreAway($scoreAway)
                        ->setTeamHome($commandHome)
                        ->setTeamAway($commandAway);

                    $this->entityManager->persist($match);
                }
            }
        }

        $this->entityManager->flush();

        return true;
    }
}

<?php

declare(strict_types=1);


namespace App\Service;


use App\Entity\Groups;
use App\Entity\Tourney;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;

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
}
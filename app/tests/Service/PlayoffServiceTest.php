<?php

declare(strict_types=1);

//namespace App\Tests\Service;

use App\Service\CommandService;
use App\Service\GroupsService;
use App\Service\PlayoffService;
use App\Service\TourneyService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlayoffServiceTest extends KernelTestCase
{

    private PlayoffService $playoffService;
    private GroupsService $groupsService;
    private TourneyService $tourneyService;
    private CommandService $commandService;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
        $container = self::$container;
        $em = $container->get(\Doctrine\ORM\EntityManagerInterface::class);
        $this->groupsService = new GroupsService($em);
        $this->commandService = new CommandService($em);
        $this->tourneyService = new TourneyService($em);
        $this->playoffService = new PlayoffService($em);
    }

    public function testFirstFourCommandsFromGroup()
    {
        $tourney = $this->tourneyService->createTourney();
        $this->groupsService->drawToGroups(
            $this->commandService->allCommands(),
            $tourney->getId()
        );

        $groupATeams = $this->playoffService->firstFourCommandsFromGroup($tourney->getId(), 'a');
        self::assertIsArray($groupATeams);
        self::assertCount(4, $groupATeams);
        foreach ($groupATeams as $team) {
            self::assertIsInt($team['teamId']);
            self::assertIsInt($team['points']);
        }
        $groupBTeams = $this->playoffService->firstFourCommandsFromGroup($tourney->getId(), 'b');
        self::assertIsArray($groupBTeams);
        self::assertCount(4, $groupBTeams);
        foreach ($groupBTeams as $team) {
            self::assertIsInt($team['teamId']);
            self::assertIsInt($team['points']);
        }
    }
}

<?php

declare(strict_types=1);


use App\Service\CommandService;
use App\Service\GroupsService;
use App\Service\TourneyService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GroupsServiceTest extends KernelTestCase
{

    private GroupsService $groupsService;
    private CommandService $commandService;
    private TourneyService $tourneyService;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
        $container = self::$container;
        $em = $container->get(\Doctrine\ORM\EntityManagerInterface::class);
        $this->groupsService = new GroupsService($em);
        $this->commandService = new CommandService($em);
        $this->tourneyService = new TourneyService($em);
    }

    public function testDrawToGroups(): void
    {
        $tourney = $this->tourneyService->createTourney();
        $result = $this->groupsService->drawToGroups(
            $this->commandService->allCommands(),
            $tourney->getId()
        );

        self::assertEquals(true, $result);
    }

    public function testCommandsInEmptyTourney(): void
    {
        $tourney = $this->tourneyService->createTourney();
        $result = $this->groupsService->commandsInTourney($tourney->getId());

        self::assertCount(2, $result);
        self::assertArrayHasKey('a', $result);
        self::assertArrayHasKey('b', $result);
        self::assertIsArray($result['a']);
        self::assertCount(0, $result['a']);
        self::assertIsArray($result['b']);
        self::assertCount(0, $result['b']);
    }

    public function testCommandsInNotEmptyTourney(): void
    {
        $tourney = $this->tourneyService->createTourney();
        $this->groupsService->drawToGroups(
            $this->commandService->allCommands(),
            $tourney->getId()
        );
        $result = $this->groupsService->commandsInTourney($tourney->getId());

        self::assertCount(2, $result);
        self::assertArrayHasKey('a', $result);
        self::assertArrayHasKey('b', $result);
        self::assertIsArray($result['a']);
        self::assertCount(8, $result['a']);
        self::assertIsArray($result['b']);
        self::assertCount(8, $result['b']);
        foreach ($result['a'] as $key=>$value) {
            self::assertIsInt($key);
            self::assertIsInt($value);
        }
        foreach ($result['b'] as $key=>$value) {
            self::assertIsInt($key);
            self::assertIsInt($value);
        }
    }
}

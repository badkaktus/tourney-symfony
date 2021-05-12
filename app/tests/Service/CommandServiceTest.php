<?php

declare(strict_types=1);


use App\Service\CommandService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommandServiceTest extends KernelTestCase
{
    private CommandService $commandService;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
        $container = self::$container;
        $em = $container->get(\Doctrine\ORM\EntityManagerInterface::class);
        $this->commandService = new CommandService($em);
    }

    public function testCommandsList()
    {
        $commands = $this->commandService->allCommands();

        self::assertIsArray($commands);
        self::assertCount(16, $commands);
    }

    public function testAllCommands()
    {
        $commands = $this->commandService->commandsList();

        self::assertIsArray($commands);
        self::assertCount(16, $commands);

        foreach ($commands as $key=>$value) {
            self::assertIsInt($key);
            self::assertIsString($value);
        }
    }
}

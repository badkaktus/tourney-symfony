<?php

declare(strict_types=1);


use App\Service\GroupsService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GroupsServiceTest extends KernelTestCase
{

    private GroupsService $groupsService;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
        $container = self::$container;
        $em = $container->get(\Doctrine\ORM\EntityManagerInterface::class);
        $this->groupsService = new GroupsService($em);
    }

    public function testDrawToGroups()
    {
    }

    public function testCommandsInTourney()
    {
    }
}

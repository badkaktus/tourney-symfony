<?php

declare(strict_types=1);


use App\Service\TourneyService;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TourneyServiceTest extends KernelTestCase
{
    private TourneyService $tourneyService;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        self::bootKernel();
        $container = self::$container;
        $em = $container->get(\Doctrine\ORM\EntityManagerInterface::class);
        $this->tourneyService = new TourneyService($em);
    }

    public function testCreateTourney()
    {
        $tourney = $this->tourneyService->createTourney();
        self::assertIsInt($tourney->getId());
        self::assertIsObject($tourney->getCreatedAt());
    }
}

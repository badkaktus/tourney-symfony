<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tourney;
use Doctrine\ORM\EntityManagerInterface;

class TourneyService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Создаем новый турнир
     *
     * @return Tourney
     */
    public function createTourney(): Tourney
    {
        $tourney = new Tourney();
        $this->entityManager->persist($tourney);
        $this->entityManager->flush();
        return $tourney;
    }
}

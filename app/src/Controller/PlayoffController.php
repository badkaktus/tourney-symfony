<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Groups;
use App\Entity\Matches;
use App\Service\PlayoffService;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Tools\Results;

class PlayoffController extends AbstractController
{
    public function __construct(
        private PlayoffService $playoffService
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('/playoff/results', name: 'playoff_results', methods: ['POST'])]
    public function index(
        Request $request
    ): Response {
        if (
            $request->isMethod('POST')
            && $request->request->get('tourney') !== null
        ) {
            $tourneyId = $request->request->get('tourney');

            if (
                $this->playoffService->generateResults($tourneyId)
            ) {
                return $this->redirectToRoute('tourney', ['id' => $tourneyId]);
            }

            throw new HttpException('Ошибка генерации результатов в плей-офф');
        }

        if ($request->request->get('tourney') !== null) {
            return $this->redirectToRoute('tourney', ['id' => $request->request->get('tourney')]);
        }

        return $this->redirectToRoute('homepage');
    }
}

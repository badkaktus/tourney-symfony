<?php

namespace App\Controller;

use App\Service\CommandService;
use App\Service\GroupsService;
use App\Service\MatchesService;
use App\Service\TourneyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Tourney;
use App\Form\TourneyFormType;
use Symfony\Component\HttpFoundation\Request;

class TourneyController extends AbstractController
{

    public function __construct(
        private TourneyService $tourneyService,
        private CommandService $commandService,
        private GroupsService $groupsService,
        private MatchesService $matchesService
    ) {
    }

    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function index(): Response
    {
        $tourney = new Tourney();
        $form = $this->createForm(TourneyFormType::class, $tourney);

        return $this->render(
            'tourney/index.html.twig',
            [
                'controller_name' => 'TourneyController',
                'tourney_form' => $form->createView()
            ]
        );
    }

    #[Route('/', name: 'create_tourney', methods: ['POST'])]
    public function createNewTourney(
        Request $request
    ): Response {
        $tourneyForm = new Tourney();
        $form = $this->createForm(TourneyFormType::class, $tourneyForm);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $newTourney = $this->tourneyService->createTourney();

                if (
                    $this->groupsService->drawToGroups(
                        $this->commandService->allCommands(),
                        $newTourney->getId()
                    )
                ) {
                    return $this->redirectToRoute('tourney', ['id' => $newTourney->getId()]);
                }
                throw new HttpException(
                    'Ошибка распределения команд по группам'
                );
            } catch (HttpException $e) {
                throw new HttpException(
                    'Ошибка создания турнира и распределения команд по группам (' . $e->getMessage() . ')'
                );
            }
        }
        return $this->redirectToRoute('homepage');
    }

    #[Route('/tourney/{id}', name: 'tourney', methods: ['GET'])]
    public function getSingleTourney(
        int $id,
        Request $request
    ): Response {
        $ret = [
            'groups' => [],
            'teamsList' => [],
            'results' => []
        ];

        $ret['groups'] = $this->groupsService->commandsInTourney($id);

        $ret['teamsList'] = $this->commandService->commandsList();

        $ret['results'] = $this->matchesService->tourneyResults($id);

        return $this->render(
            'tourney/single.html.twig',
            [
                'controller_name' => 'TourneyController',
                'id' => $id,
                'results' => $ret,
            ]
        );
    }
}

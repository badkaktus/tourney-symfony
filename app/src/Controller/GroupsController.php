<?php

namespace App\Controller;

use App\Service\GroupsService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class GroupsController extends AbstractController
{
    public function __construct(
        private GroupsService $groupsService
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('/groups/results', name: 'groups_results', methods: ['POST'])]
    public function index(
        Request $request
    ): Response {
        if (
            $request->isMethod('POST')
            && $request->request->get('group') !== null
            && $request->request->get('tourney') !== null
        ) {
            $tourneyId = $request->request->get('tourney');
            $groupLetter = $request->request->get('group');

            if (
                $this->groupsService->generateResults($tourneyId, $groupLetter)
            ) {
                return $this->redirectToRoute('tourney', ['id' => $tourneyId]);
            }

            throw new HttpException('Ошибка генерации результатов в группе');
        }

        if ($request->request->get('tourney') !== null) {
            return $this->redirectToRoute('tourney', ['id' => $request->request->get('tourney')]);
        }

        return $this->redirectToRoute('homepage');
    }
}

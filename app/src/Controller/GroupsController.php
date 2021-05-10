<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Groups;
use App\Entity\Matches;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tools\Results;

class GroupsController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    #[Route('/groups/seed', name: 'groups_seed', methods: ['POST'])]
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
            // получаем команды в группе
            $commandsInGroup = $this->getDoctrine()->getRepository(Groups::class)->findBy(
                [
                    'tourneyId' => $tourneyId,
                    'groupLetter' => $groupLetter
                ]
            );

            $commands = [];
            $commandModels = [];
            foreach ($commandsInGroup as $item) {
                $commands[] = $item->getTeamId();
                $commandModels[$item->getTeamId()] = $item;
            }
            $cntCommand = count($commands);

            foreach ($commands as $i => $teamHome) {
                if (isset($commands[$i + 1])) {
                    for ($y = $i + 1; $y < $cntCommand; $y++) {
                        if ($teamHome === $commands[$y]) {
                            continue;
                        }
                        $scoreHome = Results::getRandScore();
                        $scoreAway = Results::getRandScore();
                        switch ($scoreHome <=> $scoreAway) {
                            case -1:
                                // $scoreHome < $scoreAway
                                $commandModels[$commands[$y]]->setPoints(
                                    $commandModels[$commands[$y]]->getPoints() + 3
                                );
                                break;

                            case 1:
                                // $scoreHome > $scoreAway
                                $commandModels[$teamHome]->setPoints(
                                    $commandModels[$teamHome]->getPoints() + 3
                                );
                                break;

                            case 0:
                                // =
                                $commandModels[$commands[$y]]->setPoints(
                                    $commandModels[$commands[$y]]->getPoints() + 1
                                );
                                $commandModels[$teamHome]->setPoints(
                                    $commandModels[$teamHome]->getPoints() + 1
                                );
                                break;
                        }

                        $commandHome = $this->getDoctrine()
                            ->getRepository(Command::class)
                            ->find($teamHome);
                        $commandAway = $this->getDoctrine()
                            ->getRepository(Command::class)
                            ->find($commands[$y]);

                        $match = new Matches();
                        $match
                            ->setRound('g')
                            ->setGroupLetter($groupLetter)
                            ->setTourneyId($tourneyId)
                            ->setScoreHome($scoreHome)
                            ->setScoreAway($scoreAway)
                            ->setTeamHome($commandHome)
                            ->setTeamAway($commandAway);

                        $this->entityManager->persist($match);
                    }
                }
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('tourney', ['id' => $tourneyId]);
        }

        if ($request->request->get('tourney') !== null) {
            return $this->redirectToRoute('tourney', ['id' => $request->request->get('tourney')]);
        }

        return $this->redirectToRoute('homepage');
    }
}

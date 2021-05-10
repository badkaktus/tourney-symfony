<?php

namespace App\Controller;

use App\Entity\Command;
use App\Entity\Groups;
use App\Entity\Matches;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tools\Results;

class PlayoffController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    #[Route('/playoff/seed', name: 'playoff_seed', methods: ['POST'])]
    public function index(
        Request $request
    ): Response
    {
        if (
            $request->isMethod('POST')
            && $request->request->get('tourney') !== null
        ) {
            $tourneyId = $request->request->get('tourney');
            // получаем первые 4 команды в группе А
            $commandsInGroupA = $this->getDoctrine()->getRepository(Groups::class)->findBy(
                criteria: [
                    'tourneyId' => $tourneyId,
                    'groupLetter' => 'a'
                ],
                orderBy: ['points' => 'DESC'],
                limit: 4
            );

            $groupAPlayOff = [];
            $commandsList = [];
            foreach ($commandsInGroupA as $item) {
                $groupAPlayOff[] = [
                    'teamId' => $item->getTeamId(),
                    'points' => (int)$item->getPoints()
                ];
                $commandsList[$item->getTeamId()] = $item->getTeamId();
            }

            // получаем первые 4 команды в группе B
            $commandsInGroupB = $this->getDoctrine()->getRepository(Groups::class)->findBy(
                criteria: [
                    'tourneyId' => $tourneyId,
                    'groupLetter' => 'b'
                ],
                orderBy: ['points' => 'DESC'],
                limit: 4
            );

            $groupBPlayOff = [];
            foreach ($commandsInGroupB as $item) {
                $groupBPlayOff[] = [
                    'teamId' => $item->getTeamId(),
                    'points' => (int)$item->getPoints()
                ];
                $commandsList[$item->getTeamId()] = $item->getTeamId();
            }

            if (count($commandsInGroupA) !== 4 || count($commandsInGroupB) !== 4) {
                throw new InvalidArgumentException('нехватает команд для генерации плей-офф');
            }

            $allCommandsInPO = [];
            $y = 3;
            for ($i = 0; $i < 4; $i++) {
                $allCommandsInPO[] = $groupAPlayOff[$i];
                $allCommandsInPO[] = $groupBPlayOff[$y];
                $y--;
            }

            $playoffData = [];
            $ret = Results::roundResults($allCommandsInPO, 4, $playoffData);

            // записываем
            foreach ($ret as $round => $itemRound) {
                foreach ($itemRound as $i => $item) {
                    $commandHome = $this->getDoctrine()
                        ->getRepository(Command::class)
                        ->find($item['firstTeam']);
                    $commandAway = $this->getDoctrine()
                        ->getRepository(Command::class)
                        ->find($item['secondTeam']);
                    $match = new Matches();
                    $match
                        ->setTourneyId($tourneyId)
                        ->setRound('p')
                        ->setTeamHome($commandHome)
                        ->setTeamAway($commandAway)
                        ->setScoreHome($item['firstTeamScore'])
                        ->setScoreAway($item['secondTeamScore'])
                        ->setPlayOffRound($round);
                    $this->entityManager->persist($match);
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

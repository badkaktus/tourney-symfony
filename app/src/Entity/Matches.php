<?php

namespace App\Entity;

use App\Repository\MatchesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MatchesRepository::class)
 */
class Matches
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $tourneyId;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $round;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreHome;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreAway;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $groupLetter;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $playOffRound;

    /**
     * @ORM\ManyToOne(targetEntity=Command::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $teamHome;

    /**
     * @ORM\ManyToOne(targetEntity=Command::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $teamAway;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTourneyId(): ?int
    {
        return $this->tourneyId;
    }

    public function setTourneyId(int $tourneyId): self
    {
        $this->tourneyId = $tourneyId;

        return $this;
    }

    public function getRound(): ?string
    {
        return $this->round;
    }

    public function setRound(string $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getScoreHome(): ?int
    {
        return $this->scoreHome;
    }

    public function setScoreHome(?int $scoreHome): self
    {
        $this->scoreHome = $scoreHome;

        return $this;
    }

    public function getScoreAway(): ?int
    {
        return $this->scoreAway;
    }

    public function setScoreAway(?int $scoreAway): self
    {
        $this->scoreAway = $scoreAway;

        return $this;
    }

    public function getGroupLetter(): ?string
    {
        return $this->groupLetter;
    }

    public function setGroupLetter(?string $groupLetter): self
    {
        $this->groupLetter = $groupLetter;

        return $this;
    }

    public function getPlayOffRound(): ?int
    {
        return $this->playOffRound;
    }

    public function setPlayOffRound(?int $playOffRound): self
    {
        $this->playOffRound = $playOffRound;

        return $this;
    }

    public function getTeamHome(): ?Command
    {
        return $this->teamHome;
    }

    public function setTeamHome(?Command $teamHome): self
    {
        $this->teamHome = $teamHome;

        return $this;
    }

    public function getTeamAway(): ?Command
    {
        return $this->teamAway;
    }

    public function setTeamAway(?Command $teamAway): self
    {
        $this->teamAway = $teamAway;

        return $this;
    }
}

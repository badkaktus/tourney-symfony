<?php

namespace App\Entity;

use App\Repository\GroupsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GroupsRepository::class)
 * @ORM\Table(name="`groups`")
 */
class Groups
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
    private $groupLetter;

    /**
     * @ORM\Column(type="integer")
     */
    private $teamId;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\ManyToOne(targetEntity=Tourney::class, inversedBy="tourneyGroup")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tourney;

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

    public function getGroupLetter(): ?string
    {
        return $this->groupLetter;
    }

    public function setGroupLetter(string $groupLetter): self
    {
        $this->groupLetter = $groupLetter;

        return $this;
    }

    public function getTeamId(): ?int
    {
        return $this->teamId;
    }

    public function setTeamId(int $teamId): self
    {
        $this->teamId = $teamId;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getTourney(): ?Tourney
    {
        return $this->tourney;
    }

    public function setTourney(?Tourney $tourney): self
    {
        $this->tourney = $tourney;

        return $this;
    }
}

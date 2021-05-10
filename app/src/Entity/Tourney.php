<?php

namespace App\Entity;

use App\Repository\TourneyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TourneyRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Tourney
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;
    /**
     * @ORM\OneToMany(targetEntity=Groups::class, mappedBy="tourney")
     */
    private $tourneyGroup;
    /**
     * @ORM\OneToMany(targetEntity=Matches::class, mappedBy="matchTourney")
     */
    private $tourneyMatch;

    public function __construct()
    {
        $this->tourneyGroup = new ArrayCollection();
        $this->tourneyMatch = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|Groups[]
     */
    public function getTourneyGroup(): Collection
    {
        return $this->tourneyGroup;
    }

    public function addTourneyGroup(Groups $tourneyGroup): self
    {
        if (!$this->tourneyGroup->contains($tourneyGroup)) {
            $this->tourneyGroup[] = $tourneyGroup;
            $tourneyGroup->setTourney($this);
        }

        return $this;
    }

    public function removeTourneyGroup(Groups $tourneyGroup): self
    {
        if ($this->tourneyGroup->removeElement($tourneyGroup)) {
            // set the owning side to null (unless already changed)
            if ($tourneyGroup->getTourney() === $this) {
                $tourneyGroup->setTourney(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Matches[]
     */
    public function getTourneyMatch(): Collection
    {
        return $this->tourneyMatch;
    }

    public function addTourneyMatch(Matches $tourneyMatch): self
    {
        if (!$this->tourneyMatch->contains($tourneyMatch)) {
            $this->tourneyMatch[] = $tourneyMatch;
            $tourneyMatch->setMatchTourney($this);
        }

        return $this;
    }

    public function removeTourneyMatch(Matches $tourneyMatch): self
    {
        if ($this->tourneyMatch->removeElement($tourneyMatch)) {
            // set the owning side to null (unless already changed)
            if ($tourneyMatch->getMatchTourney() === $this) {
                $tourneyMatch->setMatchTourney(null);
            }
        }

        return $this;
    }
}

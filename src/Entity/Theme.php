<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: Quiz::class, orphanRemoval: true)]
    private Collection $quizs;

    public function __construct()
    {
        $this->quizs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Quizs>
     */
    public function getQuizs(): Collection
    {
        return $this->quizs;
    }

    public function addQuizs(Quiz $quizs): self
    {
        if (!$this->quizs->contains($quizs)) {
            $this->quizs->add($quizs);
            $quizs->setTheme($this);
        }

        return $this;
    }

    public function removeQuizs(Quiz $quizs): self
    {
        if ($this->quizs->removeElement($quizs)) {
            // set the owning side to null (unless already changed)
            if ($quizs->getTheme() === $this) {
                $quizs->setTheme(null);
            }
        }

        return $this;
    }
}

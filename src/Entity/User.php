<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;


    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Quiz::class, orphanRemoval: true)]
    private Collection $quizs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Question::class, orphanRemoval: true)]
    private Collection $questions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Answer::class, orphanRemoval: true)]
    private Collection $answers;


    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->answers = new ArrayCollection();
        $this->questions = new ArrayCollection();
        $this->quizs = new ArrayCollection();
    }





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $this->passwordHasher->hashPassword($this, $password);
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
            $quizs->setUser($this);
        }

        return $this;
    }

    public function removeQuizs(Quiz $quizs): self
    {
        if ($this->quizs->removeElement($quizs)) {
            // set the owning side to null (unless already changed)
            if ($quizs->getUser() === $this) {
                $quizs->setUser(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Questions>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestions(Question $questions): self
    {
        if (!$this->questions->contains($questions)) {
            $this->questions->add($questions);
            $questions->setUser($this);
        }

        return $this;
    }

    public function removeQuestions(Question $questions): self
    {
        if ($this->questions->removeElement($questions)) {
            // set the owning side to null (unless already changed)
            if ($questions->getUser() === $this) {
                $questions->setUser(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswers(Answer $answers): self
    {
        if (!$this->answers->contains($answers)) {
            $this->answers->add($answers);
            $answers->setUser($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answers): self
    {
        if ($this->answer->removeElement($answers)) {
            // set the owning side to null (unless already changed)
            if ($answers->getUser() === $this) {
                $answers->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
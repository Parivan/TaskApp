<?php

namespace App\Entity;

use App\Enum\UserRole;
use App\Repository\UserRepository;
use App\Entity\Team;
use App\Entity\Task;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Index(name: 'idx_user_name', columns: ['name'])]
#[UniqueEntity(fields: ['email'], message: 'This email is already registered.')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'The email cannot be empty.')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The email cannot be longer than {{ limit }} characters.'
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The name cannot be empty.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The name cannot be longer than {{ limit }} characters.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 32, enumType: UserRole::class)]
    private ?UserRole $role = null;

    /**
     * @var Collection<int, Team>
     */
    #[ORM\ManyToMany(targetEntity: Team::class, inversedBy: 'members')]
    private Collection $teams;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'assignee')]
    private Collection $tasks;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRole(): ?UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): static
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): static
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
            $team->addMember($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): static
    {
        if ($this->teams->removeElement($team)) {
            $team->removeMember($this);
        }

        return $this;
    }

    public function addTask(Task $task): static
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setAssignee($this);
        }

        return $this;
    }

    public function removeTask(Task $task): static
    {
        if ($this->tasks->removeElement($task)) {
            if ($task->getAssignee() === $this) {
                $task->setAssignee(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}

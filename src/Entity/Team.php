<?php

namespace App\Entity;

use App\Enum\TaskStatus;
use App\Repository\TeamRepository;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Index(name: 'idx_team_name', columns: ['name'])]
#[UniqueEntity(fields: ['name'], message: 'This team name is already taken. Please choose another one.')]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'teams')]
    private Collection $members;


    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'The team name cannot be longer than {{ limit }} characters.'
    )]
    #[Assert\NotBlank(message: 'The team name cannot be empty.')]
    private ?string $name = null;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\OneToMany(targetEntity: Project::class, mappedBy: 'team')]
    private Collection $projects;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
            $member->addTeam($this);
        }

        return $this;
    }

    public function removeMember(User $member): static
    {
        if ($this->members->removeElement($member)) {
            $member->removeTeam($this);
        }

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

    public function addProject(Project $project): static
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->setTeam($this);
        }

        return $this;
    }

    public function removeProject(Project $project): static
    {
        if ($this->projects->removeElement($project)) {
            if ($project->getTeam() === $this) {
                $project->setTeam(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
    public function getMemberCount(): int
    {
        return $this->members->count();
    }

    public function getTotalTasksCount(): int
    {
        $count = 0;
        foreach ($this->projects as $project) {
            $count += $project->getTasks()->count();
        }
        return $count;
    }

    public function getDoneTasksCount(): int
    {
        $count = 0;
        foreach ($this->projects as $project) {
            foreach ($project->getTasks() as $task) {
                if ($task->getStatus() === TaskStatus::DONE) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getTaskProgressPercent(): int
    {
        $total = $this->getTotalTasksCount();
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->getDoneTasksCount() / $total) * 100);
    }
}

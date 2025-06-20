<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $age = null;

    #[ORM\Column(length: 255)]
    private ?string $schedule = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $salle = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $day = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $startHour = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE)]
    private ?\DateTimeImmutable $endHour = null;

    /**
     * @var Collection<int, UserCours>
     */
    #[ORM\OneToMany(targetEntity: UserCours::class, mappedBy: 'cours')]
    private Collection $userCours;

    public function __construct()
    {
        $this->userCours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(string $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(string $schedule): static
    {
        $this->schedule = $schedule;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSalle(): ?string
    {
        return $this->salle;
    }

    public function setSalle(string $salle): static
    {
        $this->salle = $salle;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getStartHour(): ?\DateTimeImmutable
    {
        return $this->startHour;
    }

    public function setStartHour(\DateTimeImmutable $startHour): static
    {
        $this->startHour = $startHour;

        return $this;
    }

    public function getEndHour(): ?\DateTimeImmutable
    {
        return $this->endHour;
    }

    public function setEndHour(\DateTimeImmutable $endHour): static
    {
        $this->endHour = $endHour;

        return $this;
    }

    /**
     * @return Collection<int, UserCours>
     */
    public function getUserCours(): Collection
    {
        return $this->userCours;
    }

    public function addUserCour(UserCours $userCour): static
    {
        if (!$this->userCours->contains($userCour)) {
            $this->userCours->add($userCour);
            $userCour->setCours($this);
        }

        return $this;
    }

    public function removeUserCour(UserCours $userCour): static
    {
        if ($this->userCours->removeElement($userCour)) {
            // set the owning side to null (unless already changed)
            if ($userCour->getCours() === $this) {
                $userCour->setCours(null);
            }
        }

        return $this;
    }
}

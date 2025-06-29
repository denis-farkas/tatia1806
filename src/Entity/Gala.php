<?php

namespace App\Entity;

use App\Repository\GalaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalaRepository::class)]
class Gala
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null;

    /**
     * Get the ID of the gala.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the name of the gala.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the gala.
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the description of the gala.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of the gala.
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the date of the gala.
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Set the date of the gala.
     */
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}

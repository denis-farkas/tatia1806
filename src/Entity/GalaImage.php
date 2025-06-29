<?php

namespace App\Entity;

use App\Repository\GalaImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalaImageRepository::class)]
class GalaImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column(length: 255)]
    private ?string $galaname = null;

    #[ORM\Column]
    private ?int $cours = null;

    #[ORM\Column]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): static
    {
        $this->filename = $filename;

        return $this;
    }

    public function getGalaname(): ?string
    {
        return $this->galaname;
    }

    public function setGalaname(string $galaname): static
    {
        $this->galaname = $galaname;

        return $this;
    }

    public function getCours(): ?int
    {
        return $this->cours;
    }

    public function setCours(int $cours): static
    {
        $this->cours = $cours;

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
}

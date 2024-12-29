<?php

namespace App\Entity;

use App\Repository\CarMaintenanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarMaintenanceRepository::class)]
class CarMaintenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'carMaintenances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Car $car = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $maintenanceDate = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero(message: 'Le coût de la maintenance doit être positif ou nul.')]
    private ?float $cost = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getMaintenanceDate(): ?\DateTimeImmutable
    {
        return $this->maintenanceDate;
    }

    public function setMaintenanceDate(\DateTimeImmutable $maintenanceDate): static
    {
        $this->maintenanceDate = $maintenanceDate;

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

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}

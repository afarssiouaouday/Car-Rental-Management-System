<?php

namespace App\Entity;


use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CarRepository::class)]
#[Vich\Uploadable]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La marque est obligatoire.')]
    private ?string $brand = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le champ ne peut pas être vide.')]
    private ?string $model = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le prix est obligatoire.')]
    #[Assert\Positive(message: 'Le prix doit être un nombre positif.')]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'car')]
    private Collection $bookings;

    #[ORM\Column]
    #[Assert\Range(
        min: 1900,
        max: 2100,
        notInRangeMessage: 'L\'année doit être comprise entre {{ min }} et {{ max }}.'
    )]
    private ?int $year = null;

    #[ORM\Column(length: 255)]
    private ?string $license_plate = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: ['Essence', 'Diesel', 'Électrique', 'Hybride'], message: 'Veuillez choisir un type de carburant valide.')]
    private ?string $fuel_type = null;

    /**
     * @var Collection<int, CarMaintenance>
     */
    #[ORM\OneToMany(targetEntity: CarMaintenance::class, mappedBy: 'Car')]
    private Collection $carMaintenances;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    #[Vich\UploadableField(mapping: 'car_images', fileNameProperty: 'imageName')]
    #[Assert\File()]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->carMaintenances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setCar($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getCar() === $this) {
                $booking->setCar(null);
            }
        }

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getLicensePlate(): ?string
    {
        return $this->license_plate;
    }

    public function setLicensePlate(string $license_plate): static
    {
        $this->license_plate = $license_plate;

        return $this;
    }

    public function getFuelType(): ?string
    {
        return $this->fuel_type;
    }

    public function setFuelType(string $fuel_type): static
    {
        $this->fuel_type = $fuel_type;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): static
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): static
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, CarMaintenance>
     */
    public function getCarMaintenances(): Collection
    {
        return $this->carMaintenances;
    }

    public function addCarMaintenance(CarMaintenance $carMaintenance): static
    {
        if (!$this->carMaintenances->contains($carMaintenance)) {
            $this->carMaintenances->add($carMaintenance);
            $carMaintenance->setCar($this);
        }

        return $this;
    }

    public function removeCarMaintenance(CarMaintenance $carMaintenance): static
    {
        if ($this->carMaintenances->removeElement($carMaintenance)) {
            if ($carMaintenance->getCar() === $this) {
                $carMaintenance->setCar(null);
            }
        }

        return $this;
    }
}

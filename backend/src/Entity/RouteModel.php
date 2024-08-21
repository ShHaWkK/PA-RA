<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "routes")]
class RouteModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\ManyToOne(targetEntity: VehicleModel::class)]
    #[ORM\JoinColumn(name: "vehicle_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $vehicle;

    #[ORM\ManyToOne(targetEntity: UserModel::class)]
    #[ORM\JoinColumn(name: "driver_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $driver;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $start_time;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $end_time;

    #[ORM\Column(type: "string", length: 50, options: ["default" => "in_progress"], columnDefinition: "ENUM('in_progress', 'completed', 'canceled')")]
    private $status;


    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    #[ORM\OneToMany(mappedBy: "route", targetEntity: DestinationModel::class, cascade: ["persist", "remove"])]
    private $destinations;

    public function __construct()
    {
        $this->destinations = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    // Getters and Setters

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

    public function getVehicle(): ?VehicleModel
    {
        return $this->vehicle;
    }

    public function setVehicle(?VehicleModel $vehicle): self
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function getDriver(): ?UserModel
    {
        return $this->driver;
    }

    public function setDriver(?UserModel $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(?\DateTimeInterface $end_time): self
    {
        $this->end_time = $end_time;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updated_at = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setCreatedAt($value): void
    {
        $this->created_at = $value;
    }

    /**
     * @return Collection|DestinationModel[]
     */
    public function getDestinations(): Collection
    {
        return $this->destinations;
    }

    public function addDestination(DestinationModel $destination): self
    {
        if (!$this->destinations->contains($destination)) {
            $this->destinations[] = $destination;
            $destination->setRoute($this);
        }

        return $this;
    }

    public function removeDestination(DestinationModel $destination): self
    {
        if ($this->destinations->removeElement($destination)) {
            if ($destination->getRoute() === $this) {
                $destination->setRoute(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'vehicle' => $this->getVehicle()?->jsonSerialize(),
            'driver' => $this->getDriver()?->jsonSerialize(),
            'start_time' => $this->getStartTime()?->format('d-m-Y H:i:s'),
            'end_time' => $this->getEndTime()?->format('d-m-Y H:i:s'),
            'status' => $this->getStatus(),
            'created_at' => $this->getCreatedAt()?->format('d-m-Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()?->format('d-m-Y H:i:s'),
            'destinations' => $this->getDestinations()->map(function($destination) {
                return $destination->jsonSerialize();
            })->toArray(),
        ];
    }


}
?>

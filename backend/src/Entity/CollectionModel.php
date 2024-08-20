<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "collections")]
class CollectionModel implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: "UserModel")]
    #[ORM\JoinColumn(name: "volunteer_id", referencedColumnName: "id", nullable: false)]
    private $volunteer;

    #[ORM\ManyToOne(targetEntity: "VehicleModel")]
    #[ORM\JoinColumn(name: "vehicle_id", referencedColumnName: "id", nullable: false)]
    private $vehicle;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $is_completed = false;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $excel_path = null;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $collection_date;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    // Getters and setters for properties

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVolunteer(): ?UserModel
    {
        return $this->volunteer;
    }

    public function setVolunteer(UserModel $volunteer): self
    {
        $this->volunteer = $volunteer;
        return $this;
    }

    public function getVehicle(): ?VehicleModel
    {
        return $this->vehicle;
    }

    public function setVehicle(VehicleModel $vehicle): self
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function getCollectionDate(): ?\DateTimeInterface
    {
        return $this->collection_date;
    }

    public function getIsCompleted(): bool
    {
        return $this->is_completed;
    }

    public function setCollectionDate(\DateTimeInterface $collection_date): self
    {
        $this->collection_date = $collection_date;
        return $this;
    }

    public function setIsCompleted(bool $is_completed): self
    {
        $this->is_completed = $is_completed;
        return $this;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
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

    public function getExcelPath(): ?string
    {
        return $this->excel_path;
    }

    public function setExcelPath(?string $excel_path): self
    {
        $this->excel_path = $excel_path;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'volunteer_id' => $this->volunteer ? $this->volunteer->getId() : null,
            'volunteer_name' => $this->volunteer ? $this->volunteer->getFirstName() . ' ' . $this->volunteer->getLastName() : null,
            'vehicle_id' => $this->vehicle ? $this->vehicle->getId() : null,
            'vehicle_license_plate' => $this->vehicle ? $this->vehicle->getLicensePlate() : null,
            'collection_date' => $this->collection_date ? $this->collection_date->format('Y-m-d H:i:s') : null,
            'is_completed' =>  $this->getIsCompleted(),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
?>
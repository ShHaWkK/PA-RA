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
    #[ORM\JoinColumn(name: "volunteer_id", referencedColumnName: "id")]
    private $volunteer;

    #[ORM\ManyToOne(targetEntity: "VehicleModel")]
    #[ORM\JoinColumn(name: "vehicle_id", referencedColumnName: "id")]
    private $vehicle;

    #[ORM\Column(type: "datetime")]
    private $collection_date;

    #[ORM\Column(type: "datetime")]
    private $created_at;

    #[ORM\Column(type: "datetime")]
    private $updated_at;

    // Getters and setters for properties

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'volunteer_id' => $this->volunteer ? $this->volunteer->getId() : null,
            'volunteer_name' => $this->volunteer ? $this->volunteer->getFirstName() . ' ' . $this->volunteer->getLastName() : null,
            'vehicle_id' => $this->vehicle ? $this->vehicle->getId() : null,
            'vehicle_license_plate' => $this->vehicle ? $this->vehicle->getLicensePlate() : null,
            'collection_date' => $this->collection_date->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
?>
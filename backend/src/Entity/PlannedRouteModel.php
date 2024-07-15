<?php
// Path: backend/src/Entity/PlannedRouteModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "planned_routes", uniqueConstraints: [new ORM\UniqueConstraint(name: "unique_route_per_day", columns: ["delivery_id", "date"])])]
class PlannedRouteModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: DeliveryModel::class)]
    #[ORM\JoinColumn(name: "delivery_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $delivery;

    #[ORM\Column(type: "date")]
    private $date;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDelivery(): ?DeliveryModel
    {
        return $this->delivery;
    }

    public function setDelivery(?DeliveryModel $delivery): self
    {
        $this->delivery = $delivery;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
}
?>

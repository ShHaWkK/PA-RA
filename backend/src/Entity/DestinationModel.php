<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "destinations")]
class DestinationModel
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: RouteModel::class, inversedBy: "destinations")]
    #[ORM\JoinColumn(name: "route_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $route;

    #[ORM\Column(type: "string", length: 255)]
    private $address;

    #[ORM\Column(type: "string", length: 100, options: ["default" => "pending"], columnDefinition: "ENUM('association', 'individual')")]
    private $recipient_type;

    #[ORM\Column(type: "datetime")]
    private $delivery_date;


    #[ORM\Column(type: "string", length: 50, options: ["default" => "pending"], columnDefinition: "ENUM('pending', 'in_route', 'delivered')")]
    private $status;

    #[ORM\Column(type: "text", nullable: true)]
    private $comment;

    #[ORM\ManyToOne(targetEntity: WarehouseModel::class)]
    #[ORM\JoinColumn(name: "warehouse_id", referencedColumnName: "id", onDelete: "SET NULL")]
    private $warehouse;

    #[ORM\OneToMany(targetEntity: DeliveryModel::class, mappedBy: "destination", cascade: ["persist", "remove"])]
    private $deliveries;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    public function __construct()
    {
        $this->deliveries = new ArrayCollection();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoute(): ?RouteModel
    {
        return $this->route;
    }

    public function setRoute(?RouteModel $route): self
    {
        $this->route = $route;
        return $this;
    }

    public function getWarehouse(): ?WarehouseModel
    {
        return $this->warehouse;
    }

    public function setWarehouse(?WarehouseModel $warehouse): self
    {
        $this->warehouse = $warehouse;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getRecipientType(): ?string
    {
        return $this->recipient_type;
    }

    public function setRecipientType(string $recipient_type): self
    {
        $this->recipient_type = $recipient_type;
        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->delivery_date;
    }

    public function setDeliveryDate(\DateTimeInterface $delivery_date): self
    {
        $this->delivery_date = $delivery_date;
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    #[ORM\PreUpdate]
    public function setCreatedAt($value): void
    {
        $this->created_at = $value;
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

    /**
     * @return Collection|DeliveryModel[]
     */
    public function getDeliveries(): Collection
    {
        return $this->deliveries;
    }

    public function addDelivery(DeliveryModel $delivery): self
    {
        if (!$this->deliveries->contains($delivery)) {
            $this->deliveries[] = $delivery;
            $delivery->setDestination($this);
        }

        return $this;
    }

    public function removeDelivery(DeliveryModel $delivery): self
    {
        if ($this->deliveries->removeElement($delivery)) {
            // Set the owning side to null (unless already changed)
            if ($delivery->getDestination() === $this) {
                $delivery->setDestination(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'route_id' => $this->route ? $this->route->getId() : null,
            'address' => $this->address,
            'recipient_type' => $this->recipient_type,
            'delivery_date' => $this->delivery_date ? $this->delivery_date->format('Y-m-d H:i:s') : null,
            'status' => $this->status,
            'comment' => $this->comment,
            'warehouse_id' => $this->warehouse ? $this->warehouse->getId() : null,
            'warehouse_name' => $this->warehouse ? $this->warehouse->getName() : null,
        ];
    }

}
?>
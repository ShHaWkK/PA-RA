<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
#[ORM\Table(name: "stocks")]
class StockModel implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "integer")]
    private $product_id;

    #[ORM\Column(type: "integer")]
    private $quantity;

    #[ORM\Column(type: "datetime")]
    private $entry_date;

    #[ORM\Column(type: "datetime", nullable: true)]
    private $exit_date;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('available', 'in_route', 'delivered')")]
    private $availability;

    #[ORM\Column(type: "datetime")]
    private $created_at;

    #[ORM\Column(type: "datetime")]
    private $updated_at;

    #[ORM\Column(type: "integer")]
    private $warehouse_id;

    #[ORM\ManyToOne(targetEntity: WarehouseModel::class, inversedBy: "stocks")]
    #[ORM\JoinColumn(name: "warehouse_id", referencedColumnName: "id")]
    private $warehouse;

    #[ORM\ManyToOne(targetEntity: ProductModel::class)]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id")]
    private $product;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    public function setProductId(int $productId): self
    {
        $this->product_id = $productId;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getEntryDate(): ?\DateTimeInterface
    {
        return $this->entry_date;
    }

    public function setEntryDate(\DateTimeInterface $entryDate): self
    {
        $this->entry_date = $entryDate;
        return $this;
    }

    public function getExitDate(): ?\DateTimeInterface
    {
        return $this->exit_date;
    }

    public function setExitDate(?\DateTimeInterface $exitDate): self
    {
        $this->exit_date = $exitDate;
        return $this;
    }

    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    public function setAvailability(string $availability): self
    {
        $this->availability = $availability;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->created_at = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    public function getWarehouseId(): ?int
    {
        return $this->warehouse_id;
    }

    public function setWarehouseId(int $warehouse_id): self
    {
        $this->warehouse_id = $warehouse_id;
        return $this;
    }

    // Implementation of JsonSerializable
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'entry_date' => $this->entry_date->format(\DateTime::ISO8601),
            'exit_date' => $this->exit_date ? $this->exit_date->format(\DateTime::ISO8601) : null,
            'availability' => $this->availability,
            'created_at' => $this->created_at->format(\DateTime::ISO8601),
            'updated_at' => $this->updated_at->format(\DateTime::ISO8601),
            'warehouse_id' => $this->warehouse_id,
            'warehouse_name' => $this->warehouse ? $this->warehouse->getName() : null, // Serialize warehouse name
            'product_name' => $this->product ? $this->product->getName() : null, // Serialize product name
        ];
    }
}
?>
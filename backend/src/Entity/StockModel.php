<?php
// Path: backend/src/Entity/StockModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "stocks")]
class StockModel
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

    #[ORM\Column(type: "string", columnDefinition: "ENUM('available', 'in_route', 'collected')")]
    private $availability;

    #[ORM\Column(type: "datetime")]
    private $created_at;

    #[ORM\Column(type: "datetime")]
    private $updated_at;

    // Getters and setters for each property...

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
}
?>

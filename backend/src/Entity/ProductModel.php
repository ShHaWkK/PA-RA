<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "products")]
class ProductModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private $barcode;

    #[ORM\Column(type: "date")]
    private $expiration_date;

    #[ORM\Column(type: "float")]
    private $volume;

    #[ORM\Column(type: "integer")]
    private $quantity;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $qr_code_path;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $scanned;

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

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): self
    {
        $this->barcode = $barcode;
        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(\DateTimeInterface $expiration_date): self
    {
        $this->expiration_date = $expiration_date;
        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): self
    {
        $this->volume = $volume;
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

    public function getQrCodePath(): ?string
    {
        return $this->qr_code_path;
    }

    public function setQrCodePath(?string $qr_code_path): self
    {
        $this->qr_code_path = $qr_code_path;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getScanned(): ?bool
    {
        return $this->scanned;
    }

    public function setScanned(bool $scanned): self
    {
        $this->scanned = $scanned;
        return $this;
    }
}
?>

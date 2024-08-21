<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "deliveries")]
class DeliveryModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: DestinationModel::class)]
    #[ORM\JoinColumn(name: "destination_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $destination;

    #[ORM\ManyToOne(targetEntity: ProductModel::class)]
    #[ORM\JoinColumn(name: "product_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $product;

    #[ORM\Column(type: "integer")]
    private $quantity;

    #[ORM\Column(type: "string", length: 50, options: ["default" => "pending"], columnDefinition: "ENUM('pending', 'in_route', 'delivered')")]
    private $status;

    #[ORM\Column(type: "text", nullable: true)]
    private $comment;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestination(): ?DestinationModel
    {
        return $this->destination;
    }

    public function setDestination(?DestinationModel $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    public function getProduct(): ?ProductModel
    {
        return $this->product;
    }

    public function setProduct(?ProductModel $product): self
    {
        $this->product = $product;
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
}
?>

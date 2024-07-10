<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "tickets")]
class TicketModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 50)]
    private $type;

    #[ORM\Column(type: "text")]
    private $description;

    #[ORM\Column(type: "string", length: 50)]
    private $status;

    #[ORM\ManyToOne(targetEntity: UserModel::class)]
    #[ORM\JoinColumn(name: "created_by", referencedColumnName: "id")]
    private $created_by;

    #[ORM\ManyToOne(targetEntity: UserModel::class)]
    #[ORM\JoinColumn(name: "assigned_to", referencedColumnName: "id", nullable: true)]
    private $assigned_to;

    #[ORM\Column(type: "datetime")]
    private $created_at;

    #[ORM\Column(type: "datetime")]
    private $updated_at;

    // Getters and setters for each property...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
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

    public function getCreatedBy(): ?UserModel
    {
        return $this->created_by;
    }

    public function setCreatedBy(UserModel $created_by): self
    {
        $this->created_by = $created_by;
        return $this;
    }

    public function getAssignedTo(): ?UserModel
    {
        return $this->assigned_to;
    }

    public function setAssignedTo(?UserModel $assigned_to): self
    {
        $this->assigned_to = $assigned_to;
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
}
?>

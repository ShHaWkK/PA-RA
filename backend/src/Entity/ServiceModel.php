<?php
// Path: backend/src/Entity/ServiceModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "services")]
class ServiceModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\Column(type: "text")]
    private $description;

    #[ORM\Column(type: "datetime")]
    private $start_schedule;

    #[ORM\Column(type: "datetime")]
    private $end_schedule;

    #[ORM\Column(type: "integer")]
    private $capacity;

    #[ORM\Column(type: "integer", options: ["default" => 0])]
    private $current_registrations;

    #[ORM\Column(type: "string", length: 50, options: ["default" => "open"])]
    private $status;

    #[ORM\Column(type: "string", length: 255)]
    private $location;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    // Getters and setters for each property...

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStartSchedule(): ?\DateTimeInterface
    {
        return $this->start_schedule;
    }

    public function setStartSchedule(\DateTimeInterface $start_schedule): self
    {
        $this->start_schedule = $start_schedule;
        return $this;
    }

    public function getEndSchedule(): ?\DateTimeInterface
    {
        return $this->end_schedule;
    }

    public function setEndSchedule(\DateTimeInterface $end_schedule): self
    {
        $this->end_schedule = $end_schedule;
        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;
        return $this;
    }

    public function getCurrentRegistrations(): ?int
    {
        return $this->current_registrations;
    }

    public function setCurrentRegistrations(int $current_registrations): self
    {
        $this->current_registrations = $current_registrations;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
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


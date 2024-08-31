<?php
// Path: backend/src/Entity/ServiceScheduleModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Entity\ServiceModel;

#[ORM\Entity]
#[ORM\Table(name: "service_schedules")]
class ServiceScheduleModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: ServiceModel::class, inversedBy: "schedules")]
    #[ORM\JoinColumn(nullable: false)]
    private $service;
    
    #[ORM\Column(type: "datetime")]
    private $start_time;

    #[ORM\Column(type: "datetime")]
    private $end_time;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private $location;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?ServiceModel
    {
        return $this->service;
    }

    public function setService(ServiceModel $service): self
    {
        $this->service = $service;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->start_time = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->end_time = $endTime;
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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }
}
?>

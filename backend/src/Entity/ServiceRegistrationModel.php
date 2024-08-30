<?php
// Path: backend/src/Entity/ServiceRegistrationModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "service_registrations")]
class ServiceRegistrationModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "integer")]
    private $service_id;

    #[ORM\ManyToOne(targetEntity: ServiceModel::class)]
    #[ORM\JoinColumn(name: "service_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $service;

    #[ORM\Column(type: "integer")]
    private $user_id;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $registration_date;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    // Getters and setters for each property

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceId(): ?int
    {
        return $this->service_id;
    }

    public function getService(): ?ServiceModel
    {
        return $this->service;
    }

    public function setServiceId(int $serviceId): self
    {
        $this->service_id = $serviceId;
        return $this;
    }

    public function setService(?ServiceModel $service): self
    {
        $this->service = $service;
        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $userId): self
    {
        $this->user_id = $userId;
        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registration_date;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registration_date = $registrationDate;
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

    public function jsonSerialize() : array
    {
        return [
            'id' => $this->id,
            'serviceId' => $this->service ? $this->service->getId() : null,
            'service' => $this->getService()?->jsonSerialize(),
            'userId' => $this->user_id,
            'registrationDate' => $this->registration_date->format('d-m-Y H:i:s'),
            'createdAt' => $this->created_at->format('d-m-Y H:i:s'),
            'updatedAt' => $this->updated_at->format('d-m-Y H:i:s')
        ];
    }

}

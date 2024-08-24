<?php
// Path: backend/src/Entity/ServiceRegistrationModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Entity\ServiceModel;

#[ORM\Entity]
#[ORM\Table(name: "service_registrations")]
class ServiceRegistrationModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    // Remplacez la colonne integer par une association ManyToOne
    #[ORM\ManyToOne(targetEntity: "Entity\ServiceModel")]
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

    // Getters et setters pour chaque propriété...

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

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registration_date;
    }

    public function setRegistrationDate(\DateTimeInterface $registration_date): self
    {
        $this->registration_date = $registration_date;
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

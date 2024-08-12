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

    #[ORM\Column(type: "integer")]
    private $user_id;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $registration_date;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    // Getters and setters for each property

    public function getId()
    {
        return $this->id;
    }

    public function getServiceId()
    {
        return $this->service_id;
    }

    public function setServiceId($service_id)
    {
        $this->service_id = $service_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getRegistrationDate()
    {
        return $this->registration_date;
    }

    public function setRegistrationDate($registration_date)
    {
        $this->registration_date = $registration_date;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }
}

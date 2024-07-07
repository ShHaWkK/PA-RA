<?php
// Path: backend/src/Entity/AvailabilityModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Entity\UserModel;

#[ORM\Entity]
#[ORM\Table(name: "availabilities")]
class AvailabilityModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: UserModel::class, inversedBy: "availabilities")]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")]
    private $day_of_week;

    #[ORM\Column(type: "time")]
    private $start_time;

    #[ORM\Column(type: "time")]
    private $end_time;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    // Getters and setters for each property

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?UserModel
    {
        return $this->user;
    }

    public function setUser(UserModel $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getDayOfWeek(): ?string
    {
        return $this->day_of_week;
    }

    public function setDayOfWeek(string $dayOfWeek): self
    {
        $this->day_of_week = $dayOfWeek;
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
}
?>

<?php
// Path: backend/src/Entity/UserSkillModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_skills")]
class UserSkillModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "integer")]
    private $user_id;

    #[ORM\Column(type: "integer")]
    private $skill_id;

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getSkillId(): int
    {
        return $this->skill_id;
    }

    public function setSkillId(int $skill_id): self
    {
        $this->skill_id = $skill_id;
        return $this;
    }
}
?>

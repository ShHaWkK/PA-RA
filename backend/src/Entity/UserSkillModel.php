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

    #[ORM\ManyToOne(targetEntity: "UserModel", inversedBy: "user_skills")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $user;

    #[ORM\ManyToOne(targetEntity: "SkillModel", inversedBy: "user_skills")]
    #[ORM\JoinColumn(name: "skill_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private $skill;

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

    public function getSkill(): ?SkillModel
    {
        return $this->skill;
    }

    public function setSkill(SkillModel $skill): self
    {
        $this->skill = $skill;
        return $this;
    }
}

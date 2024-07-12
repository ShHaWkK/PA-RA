<?php
// Path: backend/src/Entity/SkillModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: "skills")]
class SkillModel implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $name;

    #[ORM\Column(type: "text")]
    private $description;

    #[ORM\ManyToMany(targetEntity: UserModel::class, mappedBy: "skills")]
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    // Getters and setters for each property

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

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(UserModel $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addSkill($this);
            error_log("SkillModel: Added user ID " . $user->getId() . " to skill ID " . $this->getId());
        }

        return $this;
    }

    public function removeUser(UserModel $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeSkill($this);
            error_log("SkillModel: Removed user ID " . $user->getId() . " from skill ID " . $this->getId());
        }

        return $this;
    }

    public function jsonSerialize() : array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
?>
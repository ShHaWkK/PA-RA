<?php
// Path: backend/src/Entity/UserModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: "users")]
class UserModel implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 255)]
    private $first_name;

    #[ORM\Column(type: "string", length: 255)]
    private $last_name;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private $email;

    #[ORM\Column(type: "string", length: 20, nullable: true)]
    private $phone_number;

    #[ORM\Column(type: "string", length: 255)]
    private $password;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('admin', 'volunteer', 'employee', 'manager', 'merchant')")]
    private $role;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('pending', 'approved', 'rejected')", options: ["default" => "pending"])]
    private $status;

    #[ORM\Column(type: "string", length: 6, nullable: true)]
    private $verification_code;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $is_verified;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private $created_at;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP", "onUpdate" => "CURRENT_TIMESTAMP"])]
    private $updated_at;

    #[ORM\ManyToMany(targetEntity: SkillModel::class, inversedBy: "users")]
    #[ORM\JoinTable(name: "user_skills",
        joinColumns: [new ORM\JoinColumn(name: "user_id", referencedColumnName: "id")],
        inverseJoinColumns: [new ORM\JoinColumn(name: "skill_id", referencedColumnName: "id")]
    )]
    private $skills;

    #[ORM\OneToMany(targetEntity: AvailabilityModel::class, mappedBy: "user")]
    private $availabilities;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->availabilities = new ArrayCollection();
    }

    // Getters and setters for each property

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $firstName): self
    {
        $this->first_name = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $lastName): self
    {
        $this->last_name = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phone_number = $phoneNumber;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
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

    public function getVerificationCode(): ?string
    {
        return $this->verification_code;
    }

    public function setVerificationCode(?string $verificationCode): self
    {
        $this->verification_code = $verificationCode;
        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->is_verified = $isVerified;
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

    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(SkillModel $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
            $skill->addUser($this);
        }

        return $this;
    }

    public function removeSkill(SkillModel $skill): self
    {
        if ($this->skills->removeElement($skill)) {
            $skill->removeUser($this);
        }

        return $this;
    }

    public function addAvailability(AvailabilityModel $availability): self
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities[] = $availability;
            $availability->setUser($this);
        }

        return $this;
    }

    public function removeAvailability(AvailabilityModel $availability): self
    {
        if ($this->availabilities->removeElement($availability)) {
            if ($availability->getUser() === $this) {
                $availability->setUser(null);
            }
        }

        return $this;
    }

    public function getAvailabilities()
    {
        return $this->availabilities;
    }

    public function updateFields(array $fields): self
    {
        foreach ($fields as $field => $value) {
            $method = 'set' . ucfirst($field);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'role' => $this->role,
            'status' => $this->status,
            'verification_code' => $this->verification_code,
            'is_verified' => $this->is_verified,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
?>

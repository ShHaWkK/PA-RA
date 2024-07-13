<?php
// Path: backend/src/Entity/UserTokenModel.php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

# ORM\Entity
# ORM\Table(name="user_tokens")
class UserTokenModel
{
    # ORM\Id
    # ORM\GeneratedValue(strategy="AUTO")
    # ORM\Column(type="integer")
    private $id;

    # ORM\Column(type="integer")
    private $user_id;

    # ORM\Column(type="string", length=255)
    private $token;

    # ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
    private $created_at;

    # ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
    # ORM\Version
    private $updated_at;

    # ORM\ManyToOne(targetEntity="UserModel")
    # ORM\JoinColumn(name="user_id", referencedColumnName="id")
    private $user;

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
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

    public function getUser(): ?UserModel
    {
        return $this->user;
    }

    public function setUser(?UserModel $user): self
    {
        $this->user = $user;
        return $this;
    }
}
?>

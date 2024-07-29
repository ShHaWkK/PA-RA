<?php
// Path: backend/src/Entity/MessageModel.php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "messages")]
class MessageModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: TicketModel::class, inversedBy: "messages")]
    #[ORM\JoinColumn(name: "ticket_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private $ticket;

    #[ORM\ManyToOne(targetEntity: UserModel::class)]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private $author;

    #[ORM\ManyToOne(targetEntity: UserModel::class)]
    #[ORM\JoinColumn(name: "recipient_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private $recipient;

    #[ORM\Column(type: "text")]
    private $content;

    #[ORM\Column(type: "datetime")]
    private $created_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    // Getters and setters for each property...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicket(): ?TicketModel
    {
        return $this->ticket;
    }

    public function setTicket(TicketModel $ticket): self
    {
        $this->ticket = $ticket;
        return $this;
    }

    public function getAuthor(): ?UserModel
    {
        return $this->author;
    }

    public function setAuthor(UserModel $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getRecipient(): ?UserModel
    {
        return $this->recipient;
    }

    public function setRecipient(UserModel $recipient): self
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
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
}
?>
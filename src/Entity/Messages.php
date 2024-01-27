<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Table(name: "Messages")]
#[Entity]
class Messages
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private ?int $id;

    #[ORM\Column(name: "text", type: "string", length: 255, nullable: true)]
    private ?string $text;

    #[ORM\ManyToOne(targetEntity: "Users")]
    #[ORM\JoinColumn(name: "destinataire", referencedColumnName: "id")]
    private ?Users $destinataire;

    #[ORM\ManyToOne(targetEntity: "Users")]
    #[ORM\JoinColumn(name: "source", referencedColumnName: "id")]
    private ?Users $source;

    #[ORM\Column(name: "date", type: "date", nullable: true)]
    private ?\DateTimeInterface $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDestinataire(): ?Users
    {
        return $this->destinataire;
    }

    public function setDestinataire(?Users $destinataire): self
    {
        $this->destinataire = $destinataire;
        return $this;
    }

    public function getSource(): ?Users
    {
        return $this->source;
    }

    public function setSource(?Users $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}

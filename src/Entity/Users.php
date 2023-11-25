<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;


use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'users')]
#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users implements UserInterface

{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    private ?int $id;

    #[ORM\Column(name: "nom", type: "string", length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Le Nom ne doit pas contenir de chiffres.'
    )]
    private ?string $nom;

    #[ORM\Column(name: "prenom", type: "string", length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: '/\d/',
        match: false,
        message: 'Le Prénom ne doit pas contenir de chiffres.'
    )]
    private ?string $prenom;

    #[ORM\Column(name: "mail", type: "string", length: 255, nullable: true)]
    #[Assert\Email(message: 'Veuillez entrer un email valide.')]
    private ?string $mail;

    #[ORM\Column(name: "numeroTelephone", type: "string", length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: '/^\d+$/',
        message: 'Le numéro de téléphone doit contenir uniquement des chiffres.'
    )]
    private ?string $numerotelephone;

    #[ORM\Column(name: "role", type: "string", length: 255, nullable: true)]
    private ?string $role;

    #[ORM\Column(name: "motDePasse", type: "string", length: 255, nullable: true)]
    private ?string $motdepasse;

    #[ORM\Column(name: "ville", type: "string", length: 100, nullable: true)]
    private ?string $ville;

    #[ORM\Column(name: "sexe", type: "string", length: 10, nullable: true)]
    private ?string $sexe;

    #[ORM\Column(name: "profile_image", type: "string", length: 255, nullable: true)]
    private $profileImage;

    /**
     * @Vich\UploadableField(mapping="profile_images", fileNameProperty="profileImage")
     * @var File
     */
    private $profileImageFile;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getNumerotelephone(): ?string
    {
        return $this->numerotelephone;
    }

    public function setNumerotelephone(?string $numerotelephone): static
    {
        $this->numerotelephone = $numerotelephone;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getMotdepasse(): ?string
    {
        return $this->motdepasse;
    }

    public function setMotdepasse(?string $motdepasse): static
    {
        $this->motdepasse = $motdepasse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(?string $profileImage): self
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function getProfileImageFile(): ?File
    {
        return $this->profileImageFile;
    }

    public function setProfileImageFile(?File $profileImageFile = null): void
    {
        $this->profileImageFile = $profileImageFile;

        if ($profileImageFile) {
            // It's required that at least one field changes if you are using doctrine
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function getSalt()
    {
        // Vous n'avez pas besoin de sel avec les algorithmes modernes de hachage
        return null;
    }

    public function eraseCredentials()
{
    // Cette méthode est nécessaire pour implémenter l'interface UserInterface,
    // mais elle n'a pas besoin de faire quoi que ce soit ici
}
public function getRoles()
{
    // Retournez les rôles de l'utilisateur sous forme de tableau
    return [$this->role];
}

public function getPassword()
{
    // Retournez le mot de passe de l'utilisateur
    return $this->motdepasse;
}

public function getUsername()
{
    // Retournez le nom d'utilisateur ou l'adresse e-mail de l'utilisateur
    return $this->mail;
}




}

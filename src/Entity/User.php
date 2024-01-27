<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorToken;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface as Google2FAInterface;






use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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
    private ?string $email = null;

    #[ORM\Column(name: "role", type: "string", length: 255, nullable: true)]
    private ?string $role;

    #[ORM\Column(name: "numeroTelephone", type: "string", length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: '/^\d+$/',
        message: 'Le numéro de téléphone doit contenir uniquement des chiffres.'
    )]
    private ?string $numerotelephone;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

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

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column]
    private ?string $signature = null;

    #[ORM\Column(type: 'boolean')]
    private $status;



    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }
    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): self
    {
        $this->signature = $signature;

        return $this;
    }


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

    public function getNumerotelephone(): ?string
    {
        return $this->numerotelephone;
    }

    public function setNumerotelephone(?string $numerotelephone): static
    {
        $this->numerotelephone = $numerotelephone;

        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        // Retournez les rôles de l'utilisateur sous forme de tableau
        return [$this->role];
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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}

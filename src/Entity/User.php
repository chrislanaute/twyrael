<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields = {"email"},
 *  message = "Cet email n'est pas disponible."
 * )
 * @UniqueEntity(
 *  fields = {"nickname"},
 *  message = "Pseudo indisponible, veuillez en saisir un autre."
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="2", max="12", minMessage="Votre nom d'utilisateur doit contenir plus de {{ limit }} caractères.", maxMessage="Votre nom d'utilisateur ne doit pas excéder {{ limit }} caractères.")
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="L'adresse email '{{ value }}' n'est pas valide.")
     * @Assert\Length(max="60", maxMessage="L'adresse mail ne peut pas excéder {{ limit }} caractères.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", max="24", minMessage="Votre mot de passe doit contenir plus de {{ limit }} caractères.", maxMessage="Votre mot de passe ne doit pas excéder {{ limit }} caractères.")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Votre mot de passe n'est pas identique.")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min="12", max="280", minMessage="Votre description doit contenir plus de {{ limit }} caractères.", maxMessage="Votre description ne doit pas excéder {{ limit }} caractères.")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={"image/jpeg", "image/gif", "image/png", "image/bmp"}, maxSize="2048k", mimeTypesMessage="Le fichier spécifié ne possède pas un format supporté. Les extensions autorisées sont JPEG, GIF, PNG ou BMP.", maxSizeMessage="Votre image ne doit pas excéder {{ limit }} {{ suffix }}.")
     */
    private $image;

    /**
     * @ORM\Column(type="boolean")
     */
    private $public;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function eraseCredentials() { }

    public function getSalt() { }

    public function getRoles() {
        return ['ROLE_USER'];
    }

    public function getUsername() {
        $this->getNickname();
    }
}

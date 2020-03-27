<?php

namespace App\Entity;

use App\Entity\Comments;
use App\Entity\Training;
use App\Entity\CalendarEvent;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"listUserSimple"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"listUserFull","listUserSimple"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"listUserFull","listUserSimple"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"listUserFull","listUserSimple"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Groups({"listUserFull","listUserSimple"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"listUserFull","listUserSimple"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     * @Groups({"listUserFull","listUserSimple"})
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"listUserFull","listUserSimple"})
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CalendarEvent", mappedBy="idUser", orphanRemoval=false)
     */
    private $ownCalendarEvents;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Training", mappedBy="participants")
     */
    private $studentTrainings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Training", mappedBy="teacher")
     */
    private $teacherTrainings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comments", mappedBy="user")
     */
    private $comments;

    public function __construct()
    {
        $this->ownCalendarEvents = new ArrayCollection();
        $this->studentTrainings = new ArrayCollection();
        $this->teacherTrainings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection|CalendarEvent[]
     */
    public function getOwnCalendarEvents(): Collection
    {
        return $this->ownCalendarEvents;
    }

    public function addOwnCalendarEvent(CalendarEvent $ownCalendarEvent): self
    {
        if (!$this->ownCalendarEvents->contains($ownCalendarEvent)) {
            $this->ownCalendarEvents[] = $ownCalendarEvent;
            $ownCalendarEvent->setIdUser($this);
        }

        return $this;
    }

    public function removeOwnCalendarEvent(CalendarEvent $ownCalendarEvent): self
    {
        if ($this->ownCalendarEvents->contains($ownCalendarEvent)) {
            $this->ownCalendarEvents->removeElement($ownCalendarEvent);
            // set the owning side to null (unless already changed)
            if ($ownCalendarEvent->getIdUser() === $this) {
                $ownCalendarEvent->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Training[]
     */
    public function getStudentTrainings(): Collection
    {
        return $this->studentTrainings;
    }

    public function addStudentTraining(Training $studentTraining): self
    {
        if (!$this->studentTrainings->contains($studentTraining)) {
            $this->studentTrainings[] = $studentTraining;
            $studentTraining->addParticipant($this);
        }

        return $this;
    }

    public function removeStudentTraining(Training $studentTraining): self
    {
        if ($this->studentTrainings->contains($studentTraining)) {
            $this->studentTrainings->removeElement($studentTraining);
            $studentTraining->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Training[]
     */
    public function getTeacherTrainings(): Collection
    {
        return $this->teacherTrainings;
    }

    public function addTeacherTraining(Training $teacherTraining): self
    {
        if (!$this->teacherTrainings->contains($teacherTraining)) {
            $this->teacherTrainings[] = $teacherTraining;
            $teacherTraining->setTeacher($this);
        }

        return $this;
    }

    public function removeTeacherTraining(Training $teacherTraining): self
    {
        if ($this->teacherTrainings->contains($teacherTraining)) {
            $this->teacherTrainings->removeElement($teacherTraining);
            // set the owning side to null (unless already changed)
            if ($teacherTraining->getTeacher() === $this) {
                $teacherTraining->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
}

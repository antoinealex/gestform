<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrainingRepository")
 */
class Training
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"listTraining", "Trainingdetails"})
     */
    private $startTraining;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"listTraining", "Trainingdetails"})
     */
    private $endTraining;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"Trainingdetails"})
     */
    private $maxStudent;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"Trainingdetails"})
     */
    private $pricePerStudent;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"Trainingdetails"})
     */
    private $trainingDescription;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="studentTrainings")
     * @Groups({"Trainingdetails"})
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="teacherTrainings")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"listTraining", "Trainingdetails"})
     */
    private $teacher;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"listTraining", "Trainingdetails"})
     */
    private $subject;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTraining(): ?\DateTimeInterface
    {
        return $this->startTraining;
    }

    public function setStartTraining(\DateTimeInterface $startTraining): self
    {
        $this->startTraining = $startTraining;

        return $this;
    }

    public function getEndTraining(): ?\DateTimeInterface
    {
        return $this->endTraining;
    }

    public function setEndTraining(\DateTimeInterface $endTraining): self
    {
        $this->endTraining = $endTraining;

        return $this;
    }

    public function getMaxStudent(): ?int
    {
        return $this->maxStudent;
    }

    public function setMaxStudent(int $maxStudent): self
    {
        $this->maxStudent = $maxStudent;

        return $this;
    }

    public function getPricePerStudent(): ?float
    {
        return $this->pricePerStudent;
    }

    public function setPricePerStudent(?float $pricePerStudent): self
    {
        $this->pricePerStudent = $pricePerStudent;

        return $this;
    }

    public function getTrainingDescription(): ?string
    {
        return $this->trainingDescription;
    }

    public function setTrainingDescription(?string $trainingDescription): self
    {
        $this->trainingDescription = $trainingDescription;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participants->contains($participant)) {
            $this->participants->removeElement($participant);
        }

        return $this;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }
}

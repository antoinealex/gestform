<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CalendarEventRepository")
 */
class CalendarEvent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startEvent;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endEvent;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $eventDescription;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ownCalendarEvents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $userInvited;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartEvent(): ?\DateTimeInterface
    {
        return $this->startEvent;
    }

    public function setStartEvent(\DateTimeInterface $startEvent): self
    {
        $this->startEvent = $startEvent;

        return $this;
    }

    public function getEndEvent(): ?\DateTimeInterface
    {
        return $this->endEvent;
    }

    public function setEndEvent(\DateTimeInterface $endEvent): self
    {
        $this->endEvent = $endEvent;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEventDescription(): ?string
    {
        return $this->eventDescription;
    }

    public function setEventDescription(?string $eventDescription): self
    {
        $this->eventDescription = $eventDescription;

        return $this;
    }

    public function getuser(): ?User
    {
        return $this->user;
    }

    public function setuser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getuserInvited(): ?User
    {
        return $this->userInvited;
    }

    public function setuserInvited(?User $userInvited): self
    {
        $this->userInvited = $userInvited;

        return $this;
    }
}

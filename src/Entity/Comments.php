<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentsRepository")
 */
class Comments
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"listComments"})
     */
    private $titleComment;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"listComments"})
     */
    private $bodyComment;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"listComments"})
     */
    private $dateComment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"listComments"})
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitleComment(): ?string
    {
        return $this->titleComment;
    }

    public function setTitleComment(string $titleComment): self
    {
        $this->titleComment = $titleComment;

        return $this;
    }

    public function getBodyComment(): ?string
    {
        return $this->bodyComment;
    }

    public function setBodyComment(?string $bodyComment): self
    {
        $this->bodyComment = $bodyComment;

        return $this;
    }

    public function getDateComment(): ?\DateTimeInterface
    {
        return $this->dateComment;
    }

    public function setDateComment(\DateTimeInterface $dateComment): self
    {
        $this->dateComment = $dateComment;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

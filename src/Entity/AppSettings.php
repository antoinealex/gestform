<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AppSettingsRepository")
 */
class AppSettings
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=255)
     */
    private $settingsCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $settingsValue;

    public function getSettingsCode(): ?string
    {
        return $this->settingsCode;
    }

    public function setSettingsCode(string $settingsCode): self
    {
        $this->settingsCode = $settingsCode;

        return $this;
    }

    public function getSettingsValue(): ?string
    {
        return $this->settingsValue;
    }

    public function setSettingsValue(?string $settingsValue): self
    {
        $this->settingsValue = $settingsValue;

        return $this;
    }
}

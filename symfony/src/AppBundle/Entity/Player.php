<?php

namespace AppBundle\Entity;

use AppBundle\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 */
class Player
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $account;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $platform;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $rankedTier;

    /**
     * @ORM\Column(type="float", length=255)
     */
    private $kda;

    /**
     * @ORM\Column(type="integer")
     */
    private $idTeam;

    public function getTeam(): int
    {
        return $this->idTeam;
    }

    public function setTeam(int $idTeam): void
    {
        $this->idTeam = $idTeam;
    }

    public function getRankedTier(): ?string
    {
        return $this->rankedTier;
    }

    public function setRankedTier(?string $rankedTier): void
    {
        $this->rankedTier = $rankedTier;
    }

    public function getKda(): ?float
    {
        return $this->kda;
    }

    public function setKda(?float $kda): void
    {
        $this->kda = $kda;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(string $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }
}

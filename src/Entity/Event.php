<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 *
 * @ORM\Table(name="event", indexes={@ORM\Index(name="creator", columns={"creator"})})
 * @ORM\Entity
 */
class Event implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="budget", type="float", precision=10, scale=0, nullable=false)
     */
    private $budget;

    /**
     * @var string
     *
     *   @ORM\Column(name="creator", type="string", length=20, nullable=false)
     */
    private $creator;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="event")
     */
    private $user;

    /**
     * Event constructor.
     * @param string $name
     * @param float $budget
     * @param string $creator
     * @param Collection $user
     */
    public function __construct(string $name, float $budget, string $creator, Collection $user)
    {
        $this->id = 0;
        $this->name = $name;
        $this->budget = $budget;
        $this->creator = $creator;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float
     */
    public function getBudget(): float
    {
        return $this->budget;
    }

    /**
     * @param float $budget
     */
    public function setBudget(float $budget): void
    {
        $this->budget = $budget;
    }

    /**
     * @return \User
     */
    public function getCreator(): string
    {
        return $this->creator;
    }

    /**
     * @param string $creator
     */
    public function setCreator(string $creator): void
    {
        $this->creator = $creator;
    }

    /**
     * @return Collection
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    /**
     * @param Collection $user
     */
    public function setUser(Collection $user): void
    {
        $this->user = $user;
    }

    public function jsonSerialize()
    {
        return array(
            'id'        => $this->id,
            'name'      => $this->name,
            'budget'    => $this->budget,
            'creator'   => $this->creator,
            'users'     => $this->user
        );
    }
}

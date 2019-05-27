<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Request
 *
 * @ORM\Table(name="request")
 * @ORM\Entity
 */
class Request implements \JsonSerializable {
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
     * @ORM\Column(name="user_send", type="string", length=20, nullable=false)
     */
    private $userSend;

    /**
     * @var string
     *
     * @ORM\Column(name="user_receive", type="string", length=20, nullable=false)
     */
    private $userReceive;

    /**
     * Request constructor.
     * @param string $userSend
     * @param string $userReceive
     */
    public function __construct(string $userSend, string $userReceive)
    {
        $this->id = 0;
        $this->userSend = $userSend;
        $this->userReceive = $userReceive;
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
    public function getUserSend(): string
    {
        return $this->userSend;
    }

    /**
     * @param string $userSend
     */
    public function setUserSend(string $userSend): void
    {
        $this->userSend = $userSend;
    }

    /**
     * @return string
     */
    public function getUserReceive(): string
    {
        return $this->userReceive;
    }

    /**
     * @param string $userReceive
     */
    public function setUserReceive(string $userReceive): void
    {
        $this->userReceive = $userReceive;
    }

    public function jsonSerialize()
    {
        return array(
            'id'            => $this->id,
            'userSend'      => $this->userSend,
            'userReceive'   => $this->userReceive
        );
    }
}

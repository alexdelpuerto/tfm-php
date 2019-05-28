<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Payment
 *
 * @ORM\Table(name="payment", indexes={@ORM\Index(name="buyer", columns={"buyer", "person"})})
 * @ORM\Entity
 */
class Payment implements \JsonSerializable {
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
     * @ORM\Column(name="buyer", type="string", length=20, nullable=false)
     */
    private $buyer;
    /**
     * @var string
     *
     * @ORM\Column(name="person", type="string", length=20, nullable=false)
     */
    private $person;
    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=false)
     */
    private $price;
    /**
     * @var string
     *
     * @ORM\Column(name="giftName", type="string", length=20, nullable=false)
     */
    private $giftname;
    /**
     * Payment constructor.
     * @param string $buyer
     * @param string $person
     * @param float $price
     * @param string $giftname
     */
    public function __construct(string $buyer, string $person, float $price, string $giftname)
    {
        $this->id = 0;
        $this->buyer = $buyer;
        $this->person = $person;
        $this->price = $price;
        $this->giftname = $giftname;
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
    public function getBuyer(): string
    {
        return $this->buyer;
    }
    /**
     * @param string $buyer
     */
    public function setBuyer(string $buyer): void
    {
        $this->buyer = $buyer;
    }
    /**
     * @return string
     */
    public function getPerson(): string
    {
        return $this->person;
    }
    /**
     * @param string $person
     */
    public function setPerson(string $person): void
    {
        $this->person = $person;
    }
    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
    /**
     * @return string
     */
    public function getGiftname(): string
    {
        return $this->giftname;
    }
    /**
     * @param string $giftname
     */
    public function setGiftname(string $giftname): void
    {
        $this->giftname = $giftname;
    }
    public function jsonSerialize()
    {
        return array(
            'id'            => $this->id,
            'buyer'         => $this->buyer,
            'person'        => $this->person,
            'price'         => $this->price,
            'giftname'      => $this->giftname
        );
    }
}
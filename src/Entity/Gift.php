<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * Gift
 *
 * @ORM\Entity
 */
class Gift implements \JsonSerializable {
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
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;
    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=200, nullable=true)
     */
    private $description;
    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=false)
     */
    private $price;
    /**
     * @var bool
     *
     * @ORM\Column(name="bought", type="boolean", options={"default" = false})
     */
    private $bought;
    /**
     *
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="gift")
     */
    private $event;
    /**
     * Gift constructor.
     * @param string $name
     * @param string|null $description
     * @param float $price
     * @param Event $event
     */
    public function __construct(string $name, ?string $description, float $price, Event $event)
    {
        $this->id = 0;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->bought = false;
        $this->event = $event;
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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
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
     * @return bool
     */
    public function isBought(): bool
    {
        return $this->bought;
    }
    /**
     * @param bool $bought
     */
    public function setBought(bool $bought): void
    {
        $this->bought = $bought;
    }
    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }
    /**
     * @param Event $event
     */
    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }
    public function jsonSerialize()
    {
        return array(
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'price'         => $this->price,
            'bought'        => $this->bought,
            'event'         => $this->event
        );
    }
}
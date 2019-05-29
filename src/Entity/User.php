<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"})}, indexes={@ORM\Index(name="id", columns={"id"})})
 * @ORM\Entity
 */
class User implements \JsonSerializable
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
     * @ORM\Column(name="username", type="string", length=20, nullable=false)
     */
    private $username;
    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=20, nullable=false)
     */
    private $password;
    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=true)
     */
    private $name;
    /**
     * @var string|null
     *
     * @ORM\Column(name="surname", type="string", length=25, nullable=true)
     */
    private $surname;
    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Event", inversedBy="user")
     * @ORM\JoinTable(name="users_events",
     *   joinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     *   }
     * )
     */
    private $event;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="myFriends")
     */
    private $friendsWithMe;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="friends",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
     *      )
     */
    private $myFriends;
    /**
     * User constructor.
     * @param string $username
     * @param string $password
     * @param string|null $name
     * @param string|null $surname
     */
    public function __construct(string $username, string $password, ?string $name, ?string $surname)
    {
        $this->id = 0;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
        $this->event = new ArrayCollection();
        $this->friendsWithMe = new ArrayCollection();
        $this->myFriends = new ArrayCollection();
    }

    public function addEvent(Event $event){
        $this->event[]=$event;
        return $this;
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
    public function getUsername(): string
    {
        return $this->username;
    }
    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    /**
     * @return string|null
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }
    /**
     * @param string|null $surname
     */
    public function setSurname(?string $surname): void
    {
        $this->surname = $surname;
    }
    /**
     * @return Collection
     */
    public function getEvent(): Collection
    {
        return $this->event;
    }
    /**
     * @param Collection $event
     */
    public function setEvent(Collection $event): void
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getMyFriends(): Collection
    {
        return $this->myFriends;
    }

    /**
     * @param mixed $myFriends
     */
    public function setMyFriends(Collection $myFriends): void
    {
        $this->myFriends = $myFriends;
    }

    /**
     * @return mixed
     */
    public function getFriendsWithMe(): Collection
    {
        return $this->friendsWithMe;
    }

    /**
     * @param mixed $friendsWithMe
     */
    public function setFriendsWithMe(Collection $friendsWithMe): void
    {
        $this->friendsWithMe = $friendsWithMe;
    }

    public function addFriend(User $user){
        $this->myFriends[] = $user;
        return $this;
    }

    public function jsonSerialize()
    {
        return array(
            'id'        => $this->id,
            'username'  => $this->username,
            'password'  => $this->password,
            'name'      => $this->name,
            'surname'   => $this->surname,
            'events'    => $this->event,
            'friends'   => $this->myFriends
        );
    }
}
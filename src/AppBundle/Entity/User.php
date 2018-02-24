<?php


namespace AppBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity()
 * @ORM\Table(name="appuser")
 */
class User extends EntityRepository implements ValidEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     *
     * @var string
     */
    private $post;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min="1", max="99")
     *
     * @var int
     */
    private $age;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id): User
    {
        $this->id = $id;
        return $this;
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
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getPost(): string
    {
        return $this->post;
    }

    /**
     * @param string $post
     * @return User
     */
    public function setPost(string $post): User
    {
        $this->post = $post;
        return $this;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age
     * @return User
     */
    public function setAge(int $age): User
    {
        $this->age = $age;
        return $this;
    }
}
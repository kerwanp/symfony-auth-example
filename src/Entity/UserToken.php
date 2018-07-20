<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use function Sodium\add;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Martin PAUCOT <contact@martin-paucot.fr>
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserTokenRepository")
 */
class UserToken
{

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @Assert\NotNull()
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     *
     * @Assert\NotNull()
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank()
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull()
     */
    private $expiresOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull()
     */
    private $createdAt;

    /**
     * UserToken constructor.
     *
     * @param User $user
     *
     * @throws \Exception
     */
    public function __construct(User $user)
    {
        $expiresOn = new \DateTime();
        $expiresOn->add(new \DateInterval('P1D'));
        $this->user = $user;
        $this->token = base64_encode(random_bytes(50));
        $this->expiresOn = $expiresOn;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresOn(): \DateTime
    {
        return $this->expiresOn;
    }

    /**
     * @param \DateTime $expiresOn
     */
    public function setExpiresOn(\DateTime $expiresOn): void
    {
        $this->expiresOn = $expiresOn;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

}
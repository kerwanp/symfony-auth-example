<?php


namespace App\Security;
use App\Entity\User;
use App\Entity\UserToken;
use App\Repository\UserRepository;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


/**
 * @author Martin PAUCOT <contact@martin-paucot.fr>
 */
class TokenUserProvider implements UserProviderInterface
{

    /**
     * @var UserTokenRepository
     */
    private $userTokenRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->userTokenRepository = $entityManager->getRepository(UserToken::class);
    }

    /**
     * Returns the UserToken with the given token.
     *
     * @param string $token
     * @return null|UserToken
     */
    public function getUserToken(string $token)
    {
        return $this->userTokenRepository->findOneBy(['token' => $token]);
    }

    /**
     * Returns the User with the given username (email).
     *
     * @param string $username
     * @return UserInterface|null
     */
    public function loadUserByUsername($username)
    {
        return $this->userRepository->findOneBy(['email', $username]);
    }

    /**
     * Refreshes the user.
     * We do not use this.
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function refreshUser(UserInterface $user): void
    {
        throw new UnsupportedUserException();
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return 'App\Entity\User' === $class;
    }

}
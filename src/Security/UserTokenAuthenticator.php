<?php


namespace App\Security;

use App\Entity\UserToken;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * @author Martin PAUCOT <contact@martin-paucot.fr>
 */
class UserTokenAuthenticator implements SimplePreAuthenticatorInterface
{
    const TOKEN_VALIDITY_DURATION = 10 * 3600;

    /**
     * @var HttpUtils
     */
    private $httpUtils;

    /**
     * @var UserTokenRepository
     */
    private $userTokenRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->httpUtils = new HttpUtils();
        $this->userTokenRepository = $entityManager->getRepository(UserToken::class);
    }

    /**
     * Create an anonymous Token.
     *
     * @param Request $request
     * @param $providerKey
     *
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey): PreAuthenticatedToken
    {
        $userTokenHeader = $request->headers->get('X-Auth-Token');

        if (null === $userTokenHeader) {
            return null;
        }

        return new PreAuthenticatedToken(
            'anon.',
            $userTokenHeader,
            $providerKey
        );
    }

    /**
     * Authenticate the token.
     *
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param $providerKey
     *
     * @return PreAuthenticatedToken
     * @throws \Exception
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey): PreAuthenticatedToken
    {
        if (!$userProvider instanceof TokenUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of TokenUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $tokenHeader = $token->getCredentials();
        $token = $userProvider->getUserToken($tokenHeader);

        if (null === $token || !$this->isTokenValid($token)) {
            $pre = new PreAuthenticatedToken(
                'anon.',
                $tokenHeader,
                $providerKey
            );
            $pre->setAuthenticated(true);
            return $pre;
        }

        $user = $token->getUser();

        $pre = new PreAuthenticatedToken(
            $user,
            $tokenHeader,
            $providerKey,
            $user->getRoles()
        );

        $pre->setAuthenticated(true);
        $this->userTokenRepository->refreshExpiration($token);

        return $pre;
    }

    /**
     * Check if we support token auth system.
     *
     * @param TokenInterface $token
     * @param $providerKey
     *
     * @return bool
     */
    public function supportsToken(TokenInterface $token, $providerKey): bool
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * Check the token validity.
     *
     * @param UserToken $authToken
     *
     * @return bool
     */
    private function isTokenValid(UserToken $authToken): bool
    {
        $now = new \DateTime();
        return (time() - $authToken->getExpiresOn()->getTimestamp()) < $now->getTimestamp();
    }

}
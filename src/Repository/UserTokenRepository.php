<?php


namespace App\Repository;

use App\Entity\UserToken;
use Doctrine\ORM\EntityRepository;


/**
 * @author Martin PAUCOT <contact@martin-paucot.fr>
 */
class UserTokenRepository extends EntityRepository
{

    /**
     * Refresh token expiration date.
     *
     * @param UserToken $token
     *
     * @throws \Exception
     */
    public function refreshExpiration(UserToken $token): void
    {
        $now = new \DateTime();
        $token->setExpiresOn($now->add(new \DateInterval('P1D')));
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();
    }

}
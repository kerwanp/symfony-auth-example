<?php


namespace App\Repository;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * @author Martin PAUCOT <contact@martin-paucot.fr>
 */
class UserRepository extends EntityRepository
{

    /**
     * Create a new user.
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return User
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(string $firstname, string $lastname, string $email, string $password, UserPasswordEncoderInterface $passwordEncoder): User {

        $user = new User();
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setPassword(
            $passwordEncoder->encodePassword($user, $password)
        );

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

}
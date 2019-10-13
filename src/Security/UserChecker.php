<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 12/10/2019
 * Time: 02:37
 */

namespace App\Security;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * UserChecker constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * Checks the user account before authentication.
     *
     * @param UserInterface $user
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->getEnabled()) {
            $ex = new DisabledException('Votre compte a été désactivé');
            $ex->setUser($user);
            throw $ex;

        }

        if ($user->getAllRoles()->isEmpty()) {
            $ex = new LockedException('Votre compte ne posséde aucun rôle pour accéder à l\'application');
            $ex->setUser($user);
            throw $ex;

        }
    }

    /**
     * Checks the user account after authentication.
     *
     * @param UserInterface $user
     */
    public function checkPostAuth(UserInterface $user)
    {
        $username = $user->getUsername();

        $user_checker = $this->manager->getRepository(User::class)->findOneByUsername($username);

        $user_checker->setLastLogin(new \DateTime());
        $this->manager->flush();
    }
}
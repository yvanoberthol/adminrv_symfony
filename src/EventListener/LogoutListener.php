<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 13/10/2019
 * Time: 13:18
 */

namespace App\EventListener;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    private $manager;

    /**
     * LogoutListener constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * This method is called by the LogoutListener when a user has requested
     * to be logged out. Usually, you would unset session variables, or remove
     * cookies, etc.
     * @param Request $request
     * @param Response $response
     * @param TokenInterface $token
     */
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $username = $token->getUsername();
        $user = $this->manager->getRepository(User::class)->findOneByUsername($username);

        $user->setLastDeconnexion(new \DateTime());
        $this->manager->flush();

    }
}
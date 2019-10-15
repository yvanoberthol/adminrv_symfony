<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 30/09/2019
 * Time: 22:13
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{

    /**
     * @Route("/chargement",name="chargement")
     */
    public function chargement()
    {

        return $this->render('chargement.html.twig');
    }

    /**
     * @Route({"/","/home"},name="menu_principal")
     */
    public function home()
    {

        return $this->render('menuAdministration.html.twig');
    }

    /**
     * @Route("/login",name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('menu_principal');
        }


        $model['last_username'] = $authenticationUtils->getLastUsername();
        $model['error'] = $authenticationUtils->getLastAuthenticationError();

        return $this->render('login.html.twig', $model);
        return $this->render('login.html.twig', $model);
    }

}
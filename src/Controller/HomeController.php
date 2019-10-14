<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 30/09/2019
 * Time: 22:13
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{

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

    /**
     * @Route("/changeLangue", name="change_langue")
     * @param Request $request
     * @return Response
     */
    public function changeLangue(Request $request){
        //$containerBuilder->setParameter('locale',$request->get('lang'));
        $locale = $this->getParameter('locale');

        return new Response($locale);
    }


    /**
     * @Route("/test", name="test")
     * @return Response
     */
    public function test(){
        $name = 'yvano';

        return $this->render('hello.html.twig',['name'=>$name]);
    }


}
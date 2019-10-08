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

class HomeController extends AbstractController
{

    /**
     * @Route("/",name="menu_principal")
     */
    public function home(){

       return $this->render('menuAdministration.html.twig');
    }
}
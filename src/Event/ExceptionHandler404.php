<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 13/10/2019
 * Time: 04:05
 */

namespace App\Event;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ExceptionHandler404
{
    private $twig;


    /**
     * ExceptionHandler404 constructor.
     */
    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    /**
     * @return Response
     */
    public function show(): Response
    {

        $html = $this->twig->render('error404.html.twig');
        return new Response($html);

    }
}
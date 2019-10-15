<?php
/**
 * Created by PhpStorm.
 * Client: TNC TECH
 * Date: 11/10/2019
 * Time: 23:56
 */

namespace App\Controller;


use App\Entity\Client;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    /**
     * @Route("/patients", name="patients")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clients(Request $request, PaginatorInterface $paginator)
    {
        $name = $request->get('name');
        if (isset($name) && !empty($name)) {
            $clientsList = $this->getDoctrine()->getRepository(Client::class)->clientLikeClientname($name);
        } else {
            $clientsList = $this->getDoctrine()->getRepository(Client::class)->findAll();
        }


        $model['clients'] = $paginator->paginate(
            $clientsList,
            $request->query->getInt('page', 1), 5
        );

        return $this->render('clients.html.twig', $model);
    }
}
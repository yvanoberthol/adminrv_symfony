<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 07/10/2019
 * Time: 09:56
 */

namespace App\Controller;


use App\Entity\Creneau;
use App\Entity\Medecin;
use App\Form\CreneauType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CreneauController extends AbstractController
{

    /**
     * @Route("/creneau/get/{id}", name="creneau_show")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getCreneau($id): \Symfony\Component\HttpFoundation\Response
    {

        $model['creneau'] = $this->getDoctrine()->getRepository(Creneau::class)->find($id);

        return $this->render('detailCreneau.html.twig', $model);

    }

    /**
     * @Route("/creneaux",name="creneaux")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function creneaux(Request $request, PaginatorInterface $paginator): \Symfony\Component\HttpFoundation\Response
    {
        $model['id'] = 0;
        $id = $request->get('id');
        if (isset($id) && !empty($id)) {
            $model['id'] = $id;
            $medecin = $this->getDoctrine()->getRepository(Medecin::class)->find($id);
            $creneausList = $this->getDoctrine()->getRepository(Creneau::class)->findBy(array('medecin'=>$medecin));
        } else {
            $creneausList = $this->getDoctrine()->getRepository(Creneau::class)->findAll();
        }

        $model['medecins'] = $medecin = $this->getDoctrine()->getRepository(Medecin::class)->findAll();

        $model['creneaux'] = $paginator->paginate(
            $creneausList, /* query NOT result */
            $request->query->getInt('page', 1), 5
        );

        return $this->render('creneaux.html.twig', $model);
    }

    /**
     * @Route("/formModifCreneau/{id}",name="specilaite_form_modif")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formModifCreneau($id,Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $creneau = $this->getDoctrine()->getRepository(Creneau::class)->find($id);

        $form = $this->createForm(CreneauType::class,$creneau);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $doctrine = $this->getDoctrine();

                $creneauByName = $doctrine->getRepository(Creneau::class)->findOneBy(array('nom' => $creneau->getNom()));

                if (!$creneauByName) {
                    if ($creneauByName->getId() !== $creneau->getId()) {
                        $model['creneauNameExist'] = true;
                    }
                } else {
                    $doctrine->getManager()->flush();
                    return $this->redirectToRoute('creneaux');
                }
            }

        }

        $model['id'] = $id;
        $model['form'] = $form->createView();

        return $this->render('modifCreneau.html.twig', $model);
    }


    /**
     * @Route("/deleteCreneau",name="creneau_delete",methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCreneau(Request $request): ?\Symfony\Component\HttpFoundation\RedirectResponse
    {

        $doctrine = $this->getDoctrine();
        if ($request->isMethod('POST')) {

            $id = $request->get('id');
            $creneau = $doctrine->getRepository(Creneau::class)->find($id);

            $doctrine->getManager()->remove($creneau);

            $doctrine->getManager()->flush();
            return $this->redirectToRoute('creneaux');
        }
    }

    /**
     * @Route("/creneau/form/add", name="form_creneau_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addCreneau(Request $request)
    {

        $creneau = new Creneau();

        $form = $this->createForm(CreneauType::class, $creneau);


        if ($request->isMethod('POST')) {

            $doctrine = $this->getDoctrine();
            $manager = $doctrine->getManager();

            //on hydrate l'obejet creneau
            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()){

                $intervalle = $creneau->getHeureFin()->getTimestamp() - (int)$creneau->getHeureDebut()->getTimestamp();
                $creneauByHeureDebut = $doctrine->getRepository(Creneau::class)->findOneBy(array('heure_debut' => $creneau->getHeureDebut()));
                $creneauByHeureFin = $doctrine->getRepository(Creneau::class)->findOneBy(array('heure_fin' => $creneau->getHeureFin()));
               // $creneauByHeureDebutLess = $doctrine->getRepository(Creneau::class)->creneauInIntervalle($creneau->getHeureDebut(),$creneau->getMedecin()->getId());

                if ($creneau->getHeureDebut() >= $creneau->getHeureFin()){
                    $model['hdebutGrand'] = true;
                }elseif ($intervalle < $this->getParameter('time_interval')){
                    $model['dureeIntervalle'] = true;
                }elseif ($creneauByHeureDebut){
                    $model['hdebutExists'] = true;
                }elseif ($creneauByHeureFin){
                    $model['hfinExists'] = true;
                }/*elseif ($creneauByHeureDebutLess){
                    $model['hfinLesshdebut'] = true;
                }*/else{
                    $manager->persist($creneau);
                    $manager->flush();
                    return $this->redirectToRoute('creneaux');
                }

            }

        }


        $model['form'] = $form->createView();

        return $this->render('addCreneau.html.twig', $model);
    }

}
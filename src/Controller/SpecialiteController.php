<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 30/09/2019
 * Time: 22:49
 */

namespace App\Controller;


use App\Entity\Specialite;
use App\Form\SpecialiteType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SpecialiteController extends AbstractController
{

    /**
     * @Route("/specialite/get/{id}", name="specialite_show")
     * @param $id
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSpecialite($id, Request $request, PaginatorInterface $paginator)
    {

        $model['specialite'] = $this->getDoctrine()->getRepository(Specialite::class)->find($id);

        $model['medecins'] = $paginator->paginate(
            $model['specialite']->getMedecins(), /* query NOT result */
            $request->query->getInt('page', 1), 5
        );

        return $this->render('detailSpecialite.html.twig', $model);

    }

    /**
     * @Route("/specialites",name="specialites")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function specialites(Request $request, PaginatorInterface $paginator)
    {
        $nom = $request->get('nom');
        if (isset($nom) && !empty($nom)) {
            $specialitesList = $this->getDoctrine()->getRepository(Specialite::class)->specialiteLikeNom($nom);
        } else {
            $specialitesList = $this->getDoctrine()->getRepository(Specialite::class)->findAll();
        }

        $specialites = $paginator->paginate(
            $specialitesList, /* query NOT result */
            $request->query->getInt('page', 1), 5
        );

        return $this->render('specialites.html.twig', ['specialites' => $specialites]);
    }

    /**
     * @Route("/formModifSpecialite/{id}",name="specilaite_form_modif")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formModifSpecialite($id,Request $request)
    {
        $specialite = $this->getDoctrine()->getRepository(Specialite::class)->find($id);

        $form = $this->createForm(SpecialiteType::class,$specialite);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()){
                $doctrine = $this->getDoctrine();

                $specialiteByName = $doctrine->getRepository(Specialite::class)->findOneBy(array('nom' => $specialite->getNom()));

                if (!$specialiteByName) {
                    if ($specialiteByName->getId() !== $specialite->getId()) {
                        $model['specialiteNameExist'] = true;
                    }
                } else {
                    $doctrine->getManager()->flush();
                    return $this->redirectToRoute('specialites');
                }
            }

        }

        $model['id'] = $id;
        $model['form'] = $form->createView();

        return $this->render('modifSpecialite.html.twig', $model);
    }


    /**
     * @Route("/deleteSpecialite",name="specialite_delete",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteSpecialite(Request $request)
    {

        $doctrine = $this->getDoctrine();
        if ($request->isMethod('POST')) {

            $id = $request->get('id');
            $specialite = $doctrine->getRepository(Specialite::class)->find($id);

            $doctrine->getManager()->remove($specialite);

            $doctrine->getManager()->flush();
            return $this->redirectToRoute('specialites');
        }
    }

    /**
     * @Route("/specialite/form/add", name="form_specialite_add")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSpecialite(Request $request)
    {

        $specialite = new Specialite();

        $form = $this->createForm(SpecialiteType::class, $specialite);


        if ($request->isMethod('POST')) {

            $doctrine = $this->getDoctrine();
            $manager = $doctrine->getManager();

            //on hydrate l'obejet specialite
            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()){
                $specialiteByName = $doctrine->getRepository(Specialite::class)->findOneBy(array('nom' => $specialite->getNom()));

                if (!$specialiteByName) {
                    $manager->persist($specialite);
                    $manager->flush();

                    $this->addFlash('succes','Spécialité enregistrée avec succès');
                    return $this->redirectToRoute('specialite_show',['id'=>$specialite->getId()]);

                }

                $model['specialiteNameExist'] = true;
            }

        }


        $model['form'] = $form->createView();

        return $this->render('addSpecialite.html.twig', $model);
    }


    /**
     *@route("/medecinSpecialiteStat",name="ms_stat")
     */
    public function medecinSpecialiteStat(){

        $specialiteList = $this->getDoctrine()->getRepository(Specialite::class)->findAll();


        foreach ($specialiteList as $specialite){

            $model['specialites'][] = $specialite->getNom();

            $model['counts'][] = count($specialite->getMedecins());
        }


        return $this->render('statMedecinService.html.twig',$model);
    }

    /**
     * @route("/api/stat/specialiteMedecin/{nom}")
     * @param $nom
     * @return JsonResponse
     */
    public function getMedecinSpecialite($nom){
        $specialite = $this->getDoctrine()->getRepository(Specialite::class)->findOneBy(array('nom'=>$nom));

        $medecins = array();
        foreach ($specialite->getMedecins() as $medecin){
            $medecins[] = $medecin->getId().' '.$medecin->getAllName();
        }

        return JsonResponse::create($medecins);

    }

}
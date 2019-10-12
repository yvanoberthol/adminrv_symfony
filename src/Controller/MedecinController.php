<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 02/10/2019
 * Time: 19:32
 */

namespace App\Controller;


use App\Entity\CompteMedecin;
use App\Entity\Medecin;
use App\Entity\Specialite;
use App\Form\MedecinEditType;
use App\Form\MedecinType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MedecinController extends AbstractController
{
    /**
     * @Route("/medecin/get/{id}", name="medecin_show")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMedecin($id){

        $model['medecin'] = $this->getDoctrine()->getRepository(Medecin::class)->find($id);
        $model['specialites'] = $this->getDoctrine()->getRepository(Specialite::class)->findAll();

        return $this->render('detailMedecin.html.twig',$model);

    }

    /**
     * @Route("/medecins",name="medecins")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function medecins(Request $request,PaginatorInterface $paginator){
        $nom = $request->get('nom');
        if (isset($nom) && !empty($nom)){
            $medecinsList = $this->getDoctrine()->getRepository(Medecin::class)->medecinLikeNom($nom);
        }else{
            $medecinsList = $this->getDoctrine()->getRepository(Medecin::class)->findAll();
        }

        $model['nbreMecinActif'] = 0;
        foreach ($medecinsList as $medecin){
            if ($medecin->getCompteMedecin()->getEnabled() == true){
                $model['nbreMecinActif'] +=1;
            }
        }



        $model['medecins'] = $paginator->paginate(
            $medecinsList,
            $request->query->getInt('page', 1), 5
        );

        return $this->render('medecins.html.twig',$model);
    }

    /**
     * @Route("/formModifMedecin/{id}",name="medecin_form_modif")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formModifMedecin($id,Request $request){
        $doctrine = $this->getDoctrine();

        $medecin = $doctrine->getRepository(Medecin::class)->find($id);

        if ($medecin === null){
            Throw new NotFoundHttpException('Le médecin N° '.$id.' n\'existe pas');
        }

        $form = $this->createForm(MedecinEditType::class,$medecin);

        if ($request->isMethod('POST')){

            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()){
                $medecinByMatricule = $doctrine->getRepository(Medecin::class)->findOneBy(array('matricule'=>$medecin->getMatricule()));

                if (!$medecinByMatricule){
                    if ($medecinByMatricule->getId() !== $medecin->getId()){
                        $model['medecinNameExist'] = true;
                    }
                }else{

                    $compteMedecin = $doctrine->getRepository(CompteMedecin::class)->findOneBy(array('medecin'=>$medecin));
                    $compteMedecin->setLogin($medecin->getMatricule());
                    $compteMedecin->setPassword($medecin->getAllName());

                    $doctrine->getManager()->flush();
                    return $this->redirectToRoute('medecins');
                }
            }
        }


        $model['form'] = $form->createView();

        return $this->render('modifMedecin.html.twig',$model);
    }

    /**
     * @Route("/deleteMedecin",name="medecin_delete",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteMedecin(Request $request){

        $doctrine = $this->getDoctrine();
        if ($request->isMethod('POST')){

            $id = $request->get('id');
            $medecin = $doctrine->getRepository(Medecin::class)->find($id);

            $doctrine->getManager()->remove($medecin);

            $doctrine->getManager()->flush();
            return $this->redirectToRoute('medecins');
        }
    }

    /**
     * @Route("/medecin/form/add", name="form_medecin_add")
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function formAddMedecin(Request $request){
        $medecin = new Medecin();

        $form = $this->createForm(MedecinType::class,$medecin);



        if ($request->isMethod('POST')){
            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()){

                $doctrine = $this->getDoctrine();
                $manager = $doctrine->getManager();

                $medecinByMatricule = $doctrine->getRepository(Medecin::class)->medecinByMatricule($medecin->getMatricule())
                    ->getQuery()->getResult();

                if (!$medecinByMatricule) {

                    $medecin->getImage()->upload();

                    $compteMedecin = new CompteMedecin();

                    $compteMedecin->setLogin($medecin->getMatricule());
                    $compteMedecin->setPassword($medecin->getAllName());

                    $medecin->setCompteMedecin($compteMedecin);

                    $manager->persist($medecin);
                    $manager->flush();
                    return $this->redirectToRoute('medecins');
                }
                $model['medecinNameExist'] = true;
            }

        }

        $model['form'] = $form->createView();

        return $this->render('addMedecin.html.twig',$model);
    }

    /**
     * @Route("/changerPhoto",name="changer_photo",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function changerPhoto(Request $request){

        $medecin = $this->getDoctrine()->getRepository(Medecin::class)->find($request->get('id'));

        $photo = $request->files->get('photo');
        if (!empty($photo)){
            $medecin->getImage()->setFile($photo);
        }

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('succes','Votre photo a été changée avec succès');
        return $this->redirectToRoute('medecin_show',['id'=>$medecin->getId()]);


    }


    /**
     * @Route("/addCompetence",name="add_competence",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function addCompetence(Request $request){
        $medecin = $this->getDoctrine()->getRepository(Medecin::class)->findOneBy(
            array('matricule'=>$request->get('matricule'))
        );

        $specialite = $this->getDoctrine()->getRepository(Specialite::class)->findOneBy(
            array('nom'=>$request->get('specialiteName'))
        );

        if (!$medecin->getSpecialites()->contains($specialite)){
            $medecin->getSpecialites()->add($specialite);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('succes','une nouvelle compétence lui a été ajouté avec succès');
        }else{
            $this->addFlash('error','Il possède déjà cette compétence');
        }



        return $this->redirectToRoute('medecin_show',['id'=>$medecin->getId()]);
    }

    /**
     * @Route("/suppCompetence",name="supp_competence",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function suppCompetence(Request $request){
        $medecin = $this->getDoctrine()->getRepository(Medecin::class)->findOneBy(
            array('matricule'=>$request->get('matricule'))
        );

        $specialite = $this->getDoctrine()->getRepository(Specialite::class)->findOneBy(
            array('nom'=>$request->get('specialiteName'))
        );

        $medecin->getSpecialites()->removeElement($specialite);

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('succes','La compétence a été retiré avec succès');
        return $this->redirectToRoute('medecin_show',['id'=>$medecin->getId()]);
    }



    /**
     * @Route("/compte/activeMedecin",name="medecin_compte_enabled",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function enableCompteMedecin(Request $request){

        if ($request->isMethod('POST')){
            $medecin_id = $request->get('medecin_id');
            $this->setStatutAccount($medecin_id,true);

           return $this->redirectToRoute('medecins');
        }

    }

    /**
     * @Route("/compte/deactiveMedecin",name="medecin_compte_disabled",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function disableCompteMedecin(Request $request){

        if ($request->isMethod('POST')){
            $medecin_id = $request->get('medecin_id');

            $this->setStatutAccount($medecin_id,false);

           return $this->redirectToRoute('medecins');
        }

    }

    /**
     * @param int $medecin_id
     * @param boolean $enabled
     */
    private function setStatutAccount($medecin_id, $enabled): void
    {
        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();

        $medecin = $doctrine->getRepository(Medecin::class)->find($medecin_id);

        $compte_medecin = $doctrine->getRepository(CompteMedecin::class)->findOneBy(array('medecin'=>$medecin));

        $compte_medecin->setEnabled($enabled);

        $manager->persist($compte_medecin);

        $manager->flush();
    }

}
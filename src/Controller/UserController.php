<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserType;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/users",name="users")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function users(Request $request, PaginatorInterface $paginator)
    {
        $model['username'] = '';
        $username = $request->get('username');
        if (isset($username) && !empty($username)) {
            $model['username'] = $username;
            $usersList = $this->getDoctrine()->getRepository(User::class)->userLikeUsername($username);
        } else {
            $usersList = $this->getDoctrine()->getRepository(User::class)->findAll();
        }

        $model['nbreUserActif'] = 0;
        foreach ($usersList as $user) {
            if ($user->getEnabled() === true) {
                $model['nbreUserActif'] += 1;
            }
        }


        $model['users'] = $paginator->paginate(
            $usersList,
            $request->query->getInt('page', 1), 5
        );

        $model['roles'] = $this->getDoctrine()->getRepository(Role::class)->findAll();

        return $this->render('users.html.twig', $model);
    }

    /**
     * @Route("/formModifUser",name="user_form_modif")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function formModifUser(Request $request)
    {
        $doctrine = $this->getDoctrine();

        $username = $this->getUser()->getUsername();

        $user = $doctrine->getRepository(User::class)->findOneByUsername($username);

        if ($user === null) {
            Throw new NotFoundHttpException('L\'utilisateur N° ' . $username . ' n\'existe pas');
        }

        $form = $this->createForm(UserEditType::class, $user);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()) {
                $userByUsername = $doctrine->getRepository(User::class)->findOneByUsername($user->getUsername());

                if (!$userByUsername) {
                    if ($userByUsername->getId() !== $user->getId()) {
                        $model['userNameExist'] = true;
                    }
                } else {
                    $doctrine->getManager()->flush();
                    $model['modifSucces'] = true;
                }
            }
        }

        $model['form'] = $form->createView();

        return $this->render('modifUser.html.twig', $model);
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/deleteUser",name="user_delete",methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteUser(Request $request)
    {

        $doctrine = $this->getDoctrine();
        if ($request->isMethod('POST')) {

            $id = $request->get('id');
            $user = $doctrine->getRepository(User::class)->find($id);

            $doctrine->getManager()->remove($user);

            $doctrine->getManager()->flush();
            return $this->redirectToRoute('users');
        }
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/user/form/add", name="form_user_add")
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function formAddUser(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid() && $form->isSubmitted()) {

                $doctrine = $this->getDoctrine();
                $manager = $doctrine->getManager();

                $userByUsername = $doctrine->getRepository(User::class)->findOneByUsername($user->getUsername());

                if (!$userByUsername) {

                    $manager->persist($user);
                    $manager->flush();
                    return $this->redirectToRoute('users');
                }
                $model['userNameExist'] = true;
            }

        }

        $model['form'] = $form->createView();

        return $this->render('addUser.html.twig', $model);
    }


    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/user/setAccount",name="user_set_account",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function setStatusUser(Request $request): ?RedirectResponse
    {
        $user_id = (int)$request->get('user_id');
        $enabled = (bool)$request->get('enabled');

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();

        $user = $doctrine->getRepository(User::class)->find($user_id);

        $roles = new ArrayCollection();
        if (!$user->getAllRoles()->isEmpty()) {

            foreach ($user->getAllRoles() as $role_user) {
                $roles->add($role_user->getName());
            }
        }

        if ($roles->contains('ROLE_SUPER_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez pas activer ou désactiver un compte super administrateur');
        } else {
            $user->setEnabled($enabled);
            $manager->flush();
        }

        return $this->redirectToRoute('users');
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/user/deleteRole",name="user_delete_role",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteRoleUser(Request $request)
    {

        $user_id = (int)$request->get('user_id');
        $role_id = (int)$request->get('role_id');

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->find($user_id);
        $role = $doctrine->getRepository(Role::class)->find($role_id);

        $roles = new ArrayCollection();
        if (!$user->getAllRoles()->isEmpty()) {

            foreach ($user->getAllRoles() as $role_user) {
                $roles->add($role_user->getName());
            }
        }

        if ($roles->contains('ROLE_SUPER_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez pas enlever de rôle à un super administrateur');
        } else {
            $user->removeRole($role);
            $manager->flush();
            $this->addFlash('succes', 'L\'utilisateur ' . $user->getUsername() . ' a perdu le role ' . $role->getRole());
        }

        return $this->redirectToRoute('users');
    }

    /**
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @Route("/user/addRole",name="user_add_role",methods={"POST"})
     * @param Request $request
     * @return RedirectResponse
     */
    public function addRoleUser(Request $request): RedirectResponse
    {

        $user_id = (int)$request->get('user_id');
        $role_id = (int)$request->get('role_id');

        $doctrine = $this->getDoctrine();
        $manager = $doctrine->getManager();
        $user = $doctrine->getRepository(User::class)->find($user_id);
        $role = $doctrine->getRepository(Role::class)->find($role_id);

        if ($user->getAllRoles()->contains($role)) {
            $this->addFlash('error', 'l\'utilisateur possède déjà le rôle '.$role->getRole());
        } else {
            $user->addRole($role);
            $manager->flush();

            $this->addFlash('succes', 'L\'utilisateur ' . $user->getUsername() . ' a obtenu le role ' . $role->getRole());
        }

        return $this->redirectToRoute('users');
    }

}

<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/account/modifier", name="account_password")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;
        // permet d'obtenir l'utilisateur courant (connecté)
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // récupere la valeur de l'ancien mot de passe
            $old_pwd = $form->get('old_password')->getData();
            if($encoder->isPasswordValid($user, $old_pwd)) {
                // récupere la valeur du nouveau mot de passe
                 $new_pwd = $form->get('new_password')->getData();
                 $password = $encoder->encodePassword($user, $new_pwd);

                 $user->setPassword($password);
                 // flush() transmet la requete à la BDD
                 $this->entityManager->flush();
                 $notification = "Votre mot de passe à été correctement mis à jour !";
            } else {
                $notification = "Votre mot de passe actuel n'est pas le bon";
            }

        }


        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}

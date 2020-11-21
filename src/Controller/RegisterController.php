<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

class RegisterController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
       // instanciation de la classe User
        $user = new User();
        // creation du formulaire
        $form = $this->createForm(RegisterType::class, $user);

        // permet au formulaire d'etre écouter
        $form->handleRequest($request);
        //si mon formulaire est bon 
        if($form->isSubmitted() && $form->isValid()) {
            // recupere les données soumis dans $form
            $user = $form->getData();

            // encodage du password
            $password = $encoder->encodePassword($user, $user->getPassword());
            // modification
            $user->setPassword($password);

            // appel de doctrine 
            // persist() fige la data afin qu'elle soit utilisable
            $this->entityManager->persist($user);
            // flush() transmet la requete à la BDD
            $this->entityManager->flush();
        }

          //ajout de la variable form et creation de la vue 
        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

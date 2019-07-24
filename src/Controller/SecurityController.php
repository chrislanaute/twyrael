<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationType;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/inscription", name="security_inscription")
     */
    public function inscription(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        // crée un nouvelle utilisateur
        $user = new User();

        // initialise le formulaire d'inscription
        $form = $this->createForm(RegistrationType::class, $user);

        // analyse la requête envoyé par le formulaire
        $form->handleRequest($request);

        // vérifie que le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // génère un hash en fonction du mot de passe de l'utilisateur
            $hash = $encoder->encodePassword($user, $user->getPassword());

            // enregistre le mot de passe de façon sécurisé
            $user->setPassword($hash);

            // récupère l'image uploadé par l'utilisateur
            $file = $form->get('image')->getData();
            if ($file) {
                // génère un nom unique pour l'image
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                try {
                    // déplace le fichier dans le bon répertoire
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                    // affecte le nom de l'image à l'utilisateur
                    $user->setImage($fileName);
                } catch (FileException $e) {
                    // met à null l'image de l'utilsateur
                    $user->setImage(null);
                }
            } else
                $user->setImage(null);

            // demande à doctrine de sauvegarder l'utilisateur
            $manager->persist($user);
            // exécute la requête
            $manager->flush();

            // redirige l'utilisateur vers la page de connexion
            return $this->redirectToRoute('security_connexion');
        }

        // renvoie la page d'inscription avec le formulaire créé précédemment
        return $this->render('security/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_connexion")
     */
    public function connexion()
    {
        // renvoie la page de connexion
        return $this->render('security/connexion.html.twig');
    }

    /**
     * @Route("/deconnexion", name="security_deconnexion")
     */
    public function deconnexion() { }
}


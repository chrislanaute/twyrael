<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Form\InformationType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Article;

class ProfilController extends AbstractController
{
    /**
     * @Route("/profil", name="profil")
     */
    public function index(Request $request, ObjectManager $manager)
    {
        // crée un nouvelle utilisateur
        $user = $this->getUser();

        // initialise le formulaire d'inscription
        $formInformations = $this->createForm(InformationType::class, $user);

        // analyse la requête envoyé par le formulaire
        $formInformations->handleRequest($request);

        // vérifie que le formulaire est envoyé et valide
        if ($formInformations->isSubmitted() && $formInformations->isValid()) {
            // récupère l'image uploadé par l'utilisateur
            $file = $formInformations->get('image')->getData();
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
            return $this->redirectToRoute('profil');
        }

        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findBy(['user' => $this->getUser()->getId()]);

        // renvoie la page d'inscription avec le formulaire créé précédemment
        return $this->render('profil/index.html.twig', [
            'formInformations' => $formInformations->createView(),
            'user' => $user,
            'articles' => $articles,
        ]);
    }
    
    /**
     * @Route("/profil/{id}/remove", name="profil-delete")
     */
    public function delete($id, ObjectManager $manager) {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);
        $manager->remove($article);
        $manager->flush();
        return $this->redirectToRoute('profil');
    }

    /**
     * @Route("/profil/{id}/edit", name="profil-edit")
     */
    public function edit($id, $request, ObjectManager $manager) {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);
                
        if ($article != null && $article->getUser() == $this->getUser()) {
            $form = $this->createForm(PostType::class, $article);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $article->setDate(new \dateTime());
                $manager->persist($article);
                $manager->flush();
                return $this->redirectToRoute('profil');
            }
       
        } else
            return $this->redirectToRoute('profil');
        return $this->render('profil/edit.html.twig');
    }
}


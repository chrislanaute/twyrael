<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use App\Form\InformationType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Article;
use App\Form\EditPostType;
use App\Entity\Follower;
use App\Form\ImageType;

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
        $formImage = $this->createForm(ImageType::class, $user);

        // analyse la requête envoyé par le formulaire
        $formInformations->handleRequest($request);
        $formImage->handleRequest($request);

        // vérifie que le formulaire est envoyé et valide
        if ($formInformations->isSubmitted() && $formInformations->isValid()) {
            // demande à doctrine de sauvegarder l'utilisateur
            $manager->persist($user);
            // exécute la requête
            $manager->flush();

            // redirige l'utilisateur vers la page de connexion
            return $this->redirectToRoute('profil');
        } else if ($formImage->isSubmitted() && $formImage->isValid()) {
            // récupère l'image uploadé par l'utilisateur
            $file = $formImage->get('image')->getData();
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

        $repo = $this->getDoctrine()->getRepository(Follower::class);
        $followers = $repo->findBy(['user' => $this->getUser()->getId()]);
        $followed = $repo->findBy(['follower' => $this->getUser()->getId()]);

        // renvoie la page d'inscription avec le formulaire créé précédemment
        return $this->render('profil/index.html.twig', [
            'formInformations' => $formInformations->createView(),
            'formImage' => $formImage->createView(),
            'user' => $user,
            'articles' => $articles,
            'followers' => $followers,
            'followed' => $followed
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
    public function edit($id, Request $request, ObjectManager $manager) {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);
                
        if ($article != null && $article->getUser() == $this->getUser()) {
            $form = $this->createForm(EditPostType::class, $article);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $article->setDate(new \dateTime());
                $manager->persist($article);
                $manager->flush();
                return $this->redirectToRoute('profil');
            }
       
        } else
            return $this->redirectToRoute('profil');
        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profil/{nickname}", name="profil-view")
     */
    public function view($nickname) {
        if ($nickname == $this->getUser()->getNickname()) {
            return $this->redirectToRoute('profil');
        } else {
            $repo = $this->getDoctrine()->getRepository(User::class);
            $user = $repo->findOneBy(['nickname' => $nickname]);
            $repo = $this->getDoctrine()->getRepository(Follower::class);
            $follower = $repo->findOneBy(['follower' => $this->getUser(), 'user' => $user]);
            $articles = null;
            if ($user->getPublic() || ($follower && !$follower->getBlocked())) {
                $repo = $this->getDoctrine()->getRepository(Article::class);
                $articles = $repo->findBy(['user' => $user->getId()]);
            }
            return $this->render('profil/profil.html.twig', [
                'user' => $user,
                'articles' => $articles,
                'follow' => $follower != null ? true : false ,
            ]);
        }
    }

    /**
     * @Route("/profil/{nickname}/suivre", name="profil-suivre")
     */
    public function suivre($nickname, ObjectManager $manager) {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneBy(['nickname' => $nickname]);
        $repo = $this->getDoctrine()->getRepository(Follower::class);
        $follower = $repo->findOneBy(['follower' => $this->getUser(), 'user' => $user]);
        if ($follower != null) {
            $manager->remove($follower);
        } else {
            $follower = new Follower();
            $follower->setBlocked(false);
            $follower->setUser($user);
            $follower->setFollower($this->getUser());
            $manager->persist($follower);
        }
        $manager->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/profil/{nickname}/blocked", name="profil-blocked")
     */
    public function blocked($nickname, ObjectManager $manager) {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneBy(['nickname' => $nickname]);
        $repo = $this->getDoctrine()->getRepository(Follower::class);
        $follower = $repo->findOneBy(['follower' => $user, 'user' => $this->getUser()]);
        $follower->setBlocked(!$follower->getBlocked());
        $manager->persist($follower);
        $manager->flush();
        return $this->redirectToRoute('profil');
    }
}


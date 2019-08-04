<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PostType;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, ObjectManager $manager)
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();

        $article = new Article();

        $form = $this->createForm(PostType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $regexUrl = "/(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/";
            $regexHash = "/#[A-Za-z0-9\-\.\_]+(?:)/";

            if (preg_match($regexUrl, $form->get('description')->getData(), $url))
                $article->setDescription(preg_replace($regexUrl, "<a target='_blank' href='$url[0]'>$url[0]</a> ", $form->get('description')->getData()));
            if (preg_match($regexHash, $article->getDescription(), $url)) {
                $hashtag = substr($url[0], 1);
                $article->setDescription(preg_replace($regexHash, "<a href='/hashtag/$hashtag'>$url[0]</a> ", $article->getDescription()));
            }

            $article->setUser($this->getUser());
            $article->setDate(new \dateTime());
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }
}

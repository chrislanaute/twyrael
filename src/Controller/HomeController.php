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
            $regexUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            $regexWww = "/(www).[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            $regexHash = "/#[A-Za-z0-9\-\.\_]+(?:)/";

            if (preg_match($regexUrl, $form->get('description')->getData(), $url))
                $article->setDescription(preg_replace($regexUrl, "<a href='{$url[0]}'>{$url[0]}</a> ", $form->get('description')->getData()));
            else if (preg_match($regexWww, $form->get('description')->getData(), $url))
                $article->setDescription(preg_replace($regexWww, "<a href='{$url[0]}'>{$url[0]}</a> ", $form->get('description')->getData()));
            if (preg_match($regexHash, $form->get('description')->getData(), $url)) {
                $hashtag = substr($url[0], 1);
                $article->setDescription(preg_replace($regexHash, "<a href='/hashtag/{$hashtag}'>{$url[0]}</a> ", $form->get('description')->getData()));
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

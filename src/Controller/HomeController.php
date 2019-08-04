<?php

namespace App\Controller;

//require 'vendor/autoload.php';

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
        $client = new \GuzzleHttp\Client(['verify' => 'C:\wamp\bin\php\php7.3.5\cacert.pem']);
        $res = $client->request('GET', 'https://EU.api.blizzard.com/d3/profile/FREIJA-2389/?locale=fr_FR&access_token=USCCc4IwaUmT7IbonZ08AUY8H31Tho2E0P');

        $repo = $this->getDoctrine()->getRepository(Article::class);
        if ($this->getUser())
            $articles = $repo->findByFollower($this->getUser()->getId());
        else
            $articles = $repo->findByPublic();

        $article = new Article();

        $form = $this->createForm(PostType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $regexUrl = "/(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/";
            $regexHash = "/#[A-Za-z0-9\-\.\_]+(?:)/";
            $regexDiablo = "/DP %[A-Za-z0-9\-\.\_]+#+[0-9]+(?:)/";

            if (preg_match($regexUrl, $form->get('description')->getData(), $url))
                $article->setDescription(preg_replace($regexUrl, "<a target='_blank' href='$url[0]'>$url[0]</a> ", $form->get('description')->getData()));
            if (preg_match($regexDiablo, $article->getDescription(), $url)) {
                $diablo = substr($url[0], 4);
                $article->setDescription(preg_replace($regexDiablo, "<button type='button' class='btn btn-secondary diablo' data-container='body' data-toggle='popover' data-placement='right' data-content='Null'>$diablo</button> ", $article->getDescription()));
            } else if (preg_match($regexHash, $article->getDescription(), $url)) {
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
            'articles' => $articles,
            'diablo' => $res->getBody()
        ]);
    }
}

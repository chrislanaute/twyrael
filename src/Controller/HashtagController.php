<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;

class HashtagController extends AbstractController
{
    /**
     * @Route("/hashtag/{text}", name="hashtag")
     */
    public function index($text) {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $hashtag = $repo->findByText($text);

        return $this->render('hashtag/index.html.twig', [
            'hashtag' => $hashtag,
            'word' => $text
        ]);
    }
}

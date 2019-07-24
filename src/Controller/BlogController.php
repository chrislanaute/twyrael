<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
    /**
     * @route("/monblog", name="monblog")
     */
    public function monblog ()
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'welcome friends',
        ]); 

    }


}


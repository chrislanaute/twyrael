<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

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
    public function monblog()
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'welcome friends',
        ]); 

    }
    /**
     * @route("/blog/10", name="blog_show")
     */
    public function show()
    {
        return $this->render('blog/show.html.twig');
    }
    
    /**
     * @route("/blog/new", name="blog_create")
     */
    public function create(Request $request, ObjectManager $manager)
    {   dump($request);
        if($request->request->count() >0)
            $article = new article();
            $article->setTitle($request->request->get('title'))
                    ->setContent($request->request->get('content'))
                    ->setImage($request->request->get('image'))
                    ->setCreateAt(new \dateTime());
            $manager->persist($article);
            $manager->flush();
        }
        return $this->render('blog/create.html.twig');
    }

}


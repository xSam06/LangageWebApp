<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(PostRepository $postRepository): Response
    { 

        return $this->render('main/index.html.twig', [
            'posts' => $postRepository ->findBy([],['id' => 'asc'])
        ]);
    }

 
}

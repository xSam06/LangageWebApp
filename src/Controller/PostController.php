<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{




    /**
     * @Route("/", name="app_post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }
    /**
     * @Route("/posts",name="app_mes_posts" ,methods={"GET"})
     */
    public function posts(PostRepository $postRepository):Response{
        $user = $this->getUser();
        if ($user) {
            // Récupérer les posts de l'utilisateur actuel
            $posts = $postRepository->findBy(['user' => $user]);
        }
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/new", name="app_post_new", methods={"GET", "POST"})
     *  * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request, PostRepository $postRepository): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        $user = $this->getUser();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $images = $form->get('image')->getData();
            foreach($images as $image){
                //modifier le nom de l'image et la stocker dans un fichier ainsi que dans la BDD
            $imageName = md5(uniqid()).'.'.$image->guessExtension();
            $image->move($this->getParameter('images_directory'),$imageName);
            $img = new Image();
            $img->setImageName($imageName);
            $post->setImage($img);
            $post->setUser($user);

            
            #$postRepository->add($post, true);
        $entityManager->persist($post);
        $entityManager->flush();
        $this->addFlash('success', 'Post created successfully!');
            }
            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        
    }
        return $this->renderForm('post/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
}

    /**
     * @Route("/{id}", name="app_post_show", methods={"GET"})
     */
    public function show(Post $post): Response
    {   $user = $this->getUser();
        return $this->render('post/show.html.twig', [
            'post' => $post,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_post_edit", methods={"GET", "POST"})
     *  * @Security("is_granted('ROLE_USER')")
     */
    public function edit(Request $request, Post $post, PostRepository $postRepository): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->add($post, true);

            return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_post_delete", methods={"POST"})
     *  * @Security("is_granted('ROLE_USER')")
     */
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = $post->getImage();
                // Supprimer l'entité Image  
            $name = $image->getImageName();
            unlink($this->getParameter('images_directory').'/'.$name);
            
            $postRepository->remove($post, true);
      
            
        }

        return $this->redirectToRoute('app_post_index', [], Response::HTTP_SEE_OTHER);
    }

  




}

<?php

namespace App\Controller;

use App\Entity\Comentarios;
use App\Entity\Posts;
use App\Form\ComentariosType;
use App\Form\PostsType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostsController extends AbstractController
{
    #[Route('/registrarposts', name: 'registrarposts')]
    
    public function index(Request $request, SluggerInterface $slugger)
    {
        $post = new Posts();
        $form = $this->createForm(PostsType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('foto')->getData();
            
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                
                $safeFilename = $slugger->slug($originalFilename);
                
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    throw new \Exception('UPS, Ha ocurrido un error');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setFoto($newFilename);
            }




            $user = $this->getUser();//obtener el usuario logeado
            $post->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('posts/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/posts/{id}', name: 'verposts')]

    public function VerPost($id, Request $request, PaginatorInterface $paginator){
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Posts::class)->find($id);
        $comentarios = new Comentarios();

        $queryComentarios = $em->getRepository(Comentarios::class)->BuscarComentariosDeUNPost($post->getId());
        
        $form = $this->createForm(ComentariosType::class, $comentarios);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){

            $user = $this->getUser();
            $comentarios->setPosts($post);
            $comentarios->setUser($user);
            $em->persist($comentarios);
            $em->flush();
            //$this->addFlash('Exito', Comentarios::COMENTARIO_AGREGADO_EXITOSAMENTE);
            //return $this->redirectToRoute('VerPost',['id'=>$post->getId()]);
            return $this->redirectToRoute('dashboard');

        }

        $pagination = $paginator->paginate(
            $queryComentarios, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            20 /*limit per page*/
        );

        //$post = $em->getRepository(Posts::class)->find($id);
        return $this->render('posts/verPost.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'comentarios'=>$pagination
        ]);
    }

    #[Route('/misposts', name: 'misposts')]
    public function MisPosts(){
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $posts = $em->getRepository(Posts::class)->findBy(['user' =>$user]);

        return $this->render('posts/misposts.html.twig', [
            'posts' => $posts
        ]);

    }
}

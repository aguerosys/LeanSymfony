<?php

namespace App\Controller;

use App\Entity\Comentarios;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Posts;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $user = $this->getUser();
        if($user){ //si el usuario esta logeado puede ver 
            $em = $this->getDoctrine()->getManager();
            $query = $em->getRepository(Posts::class)->BuscarPosts();
            $comentarios = $em->getRepository(Comentarios::class)->BuscarComentarios($user->getId());

            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                3 /*limit per page*/
            );



            $post = $em->getRepository(Posts::class)->findAll();  
            
            return $this->render('dashboard/index.html.twig',[
                'pagination' => $pagination,
                'comentarios' => $comentarios
            ]);
        }else{
            return $this->redirectToRoute('app_login'); //si no esta logeado lo envio al loggin
        }
        
    }
}

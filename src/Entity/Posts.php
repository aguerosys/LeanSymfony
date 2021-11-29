<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostsRepository::class)
 */
class Posts
{
    public function __construct()
    {
        $this->likes = '';
        $this->fecha_publicacion = new DateTime();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $likes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha_publicacion;

    /**
     * @ORM\Column(type="string", length=80000)
     */
    private $contenido;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comentarios", mappedBy="posts")
     */
    private $comentarios;

     /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     */
    private $user;

    public function getId()
    {
        return $this->id;
    }

    public function getTitulo()
    {
        return $this->titulo;
    }

    public function setTitulo($titulo): void
    {
        $this->titulo = $titulo;

    }

    public function getLikes()
    {
        return $this->likes;
    }

    public function setLikes($likes): void
    {
        $this->likes = $likes;

    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function setFoto($foto): void
    {
        $this->foto = $foto;

    
    }

    public function getFechaPublicacion()
    {
        return $this->fecha_publicacion;
    }

    public function setFechaPublicacion($fecha_publicacion): void
    {
        $this->fecha_publicacion = $fecha_publicacion;

    }

    public function getContenido()
    {
        return $this->contenido;
    }

    public function setContenido($contenido): void
    {
        $this->contenido = $contenido;

    }
    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
        
     
    }
}

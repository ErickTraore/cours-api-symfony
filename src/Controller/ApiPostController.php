<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\ApiPostController;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
// use Symfony\Component\HttpKernel\Exception\HttpException;
// use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
// use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
// use Symfony\Component\Debug\ExceptionHandler;
// use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface ;



class ApiPostController extends AbstractController
{
    /**
     * @Route("/api/post", name="api_post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository){
        return $this->json($postRepository->findAll(), 200, [], ['groups' => 'post:read']);
    }





      /**
     * @Route("/api/post", name="api_post_store", methods={"POST"})
     */
    public function store(Request $request, SerializerInterface $serialiser, EntityManagerInterface $em,  ValidatorInterface $validator){
        $jsonRecu = $request->getContent();
    try{
        $post = $serialiser->deserialize($jsonRecu, Post::class, 'json');
        $post->setCreatedAt(new \DateTime());
        
        $errors = $validator->validate($post);
        if(count($errors) > 0) {
           return $this->json($errors, 400);
        }

        $em->persist($post);
        $em->flush();

return $this->json($post, 201, [], ['groups' => 'post:read']);
    }
    catch(NotEncodableValueException $e){
        return $this->json([
            'status' => 400,
            'mesage' => $e->getMessage()
        ], 400);

    }
    }
}

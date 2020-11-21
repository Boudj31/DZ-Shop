<?php

namespace App\Controller;

use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager) {

        $this->entityManager = $entityManager;
        
    }
    /**
     * @Route("/nos-produits", name="products")
     */
    public function index(): Response
    {
        $products = $this->entityManager->getRepository(Products::class)->findAll();
       

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }
}

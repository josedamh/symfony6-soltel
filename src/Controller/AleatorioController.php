<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Atributos Symfony6 (#[Route]) y anotaciones Symfony5 (@Route)

#[Route('/num-aleatorios', name: 'app_aleatorio')]
/**
 * @Route("/num-aleatorios", name="app_aleatorios")
 */
class AleatorioController extends AbstractController
{
    #[Route('/num1', name: 'app_aleatorio1')]
    public function index(): Response
    {
        $numAleatorio = random_int(0, 100);

        return $this->render('aleatorio/index.html.twig', [
            'controller_name' => 'AleatorioController',
            'numeroAleatorio' => $numAleatorio,
        ]);
    }

    #[Route('/num2', name: 'app_aleatorio2')]
    public function index2(): Response
    {
        $numAleatorio = "000";

        return $this->render('aleatorio/index.html.twig', [
            'controller_name' => 'AleatorioController',
            'numeroAleatorio' => $numAleatorio,
        ]);
    }

    // #[Route('/num3', name: 'app_aleatorio3')]
    public function index3(): Response
    {
        $numAleatorio = "666";

        return $this->render('aleatorio/index.html.twig', [
            'controller_name' => 'AleatorioController',
            'numeroAleatorio' => $numAleatorio,
        ]);
    }
}

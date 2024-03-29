<?php

namespace App\Controller;

use App\Entity\Clubes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

#[Route('/clubes', name: 'app_clubes')]
class ClubesController extends AbstractController
{
    #[Route('/insertarClubes', name: 'app_insertarClubes')]
    public function index(EntityManagerInterface $gestorEntidades): Response
    {
        // endpoint de ejemplo: http://127.0.0.1:8000/clubes/insertarClubes
        $clubes = array(
            "betis" => array (
                "cif" => "12345678A",
                "nombre" => "Real Betis",
                "fundacion" => "1907-09-12",
                "num_socios" => 62000,
                "estadio" => "Benito Villamarín"
            ),
            "sevilla" => array (
                "cif" => "23456789A",
                "nombre" => "Sevilla FC",
                "fundacion" => "1905-10-14",
                "num_socios" => 45000,
                "estadio" => "Sánchez Pizjuan"
            )
        );

        foreach ($clubes as $registro) {
            try {
                $club = new Clubes();
                $club->setCif($registro['cif']);
                $club->setNombre($registro['nombre']);

                // Para las fechas, creamos el objeto DateTime
                $fundacion = new DateTime($registro['fundacion']);
                $club->setFundacion($fundacion);

                $club->setNumSocios($registro['num_socios']);
                $club->setEstadio($registro['estadio']);

                // Hago el insert, persistiendo el registro
                $gestorEntidades->persist($club);
                $gestorEntidades->flush();
            } catch (UniqueConstraintViolationException $e) {
                return new Response("<h1>Error clave primaria duplicada </h1>");
            }
            
        }
        return new Response("<h1>Clubes insertados </h1>");
    }
}

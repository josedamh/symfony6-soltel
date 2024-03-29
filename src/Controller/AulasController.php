<?php

namespace App\Controller;

use App\Entity\Aulas;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Importamos clases abstractas de registros
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;




#[Route('/aulas', name: 'app_aulas_')] // enrutamiento con atributos, también lo hemos hecho con yaml, xml,,,
class AulasController extends AbstractController
{
    #[Route('/{numAula}/{capacidad}/{docente}/{hardware}', name: 'insertarAula')] // Insertar datos con parámetros

    public function index(
        int $numAula, 
        int $capacidad, 
        String $docente, 
        bool $hardware,
        EntityManagerInterface $gestorEntidades
        ): Response {
        //endpoint de ejemplo:
        // http://127.0.0.1:8000/aulas/23/15/Iván Rodríguez/1

        $aula = new Aulas();
        // Settear los campos
        $aula->setNumAula($numAula);
        $aula->setCapacidad($capacidad);
        $aula->setDocente($docente);
        $aula->setHardware($hardware);

        // Hago el insert, persistiendo el registro
        $gestorEntidades->persist($aula);
        $gestorEntidades->flush();

        return new Response("<h1>Registro insertado: $numAula, $capacidad, $docente, $hardware</h1>");
        /*
        return $this->render('aulas/index.html.twig', [
            'controller_name' => 'AulasController',
        ]);
        */
    }
    #[Route('/insertarAulas', name: 'insertarAulas')]
    public function insertarAulas(ManagerRegistry $gestorFilas) : Response 
    {
        // endpoint de ejemplo: http://127.0.0.1:8000/aulas/insertarAulas
        $gestorEntidades = $gestorFilas->getManager();
        $aulas = array(
            "aula1" => array (
                "num_aula" => 21,
                "capacidad" => 2,
                "docente" => "Isabel Álvarez",
                "hardware" => 0
            ),
            "aula2" => array (
                "num_aula" => 22,
                "capacidad" => 15,
                "docente" => "Ignacio Mejias",
                "hardware" => 0
            ),
        );

        foreach ($aulas as $clave => $registro){
            $aula = new Aulas;
            // Voy asignando los distintos campos...
            $aula->setNumAula($registro['num_aula']);
            $aula->setCapacidad($registro['capacidad']);
            $aula->setDocente($registro['docente']);
            $aula->setHardware($registro['hardware']);

            // Hago el insert, persistiendo el registro
            $gestorEntidades->persist($aula);
            $gestorEntidades->flush();
        }

        return new Response("<h1>Registros insertados </h1>");
    }

    #[Route('/consultarAulas', name: 'consultarAulas')]
    public function consultarAulas(ManagerRegistry $gestorFilas) : Response 
    {
        // endpoint de ejemplo: http://127.0.0.1:8000/aulas/consultarAulas
        // Saco el gestor de entidades a partir del gestor de filas que es más generico
        
        // SELECT con twig
        
        $gestorEntidades = $gestorFilas->getManager();
        // Desde el gestor de entidades, saco el repositorio de la clase
        $repoAulas = $gestorEntidades->getRepository(Aulas::class);
        $filasAulas = $repoAulas->findAll();

        return $this->render('aulas/index.html.twig', [
            'controller_name' => 'Controlador Aulas',
            'tabla' => $filasAulas,
        ]);
    }


    #[Route('/actualizarAula/{numAula}/{capacidad}/{docente}/{hardware}', name: 'actualizarAula')]
    public function actualizarAula(ManagerRegistry $gestorFilas, $numAula, $capacidad, $docente, $hardware) : Response 
    {
        // endpoint de ejemplo: http://127.0.0.1:8000/aulas/actualizarAula/21/1/Isabel Álvarez Sánchez/1
        /*
        DE FORMA MÁS PROFESIONAL, VIRGUERO
        $gestorEntidades = $gestorFilas->getManager();
        $aula = $gestorEntidades->getRepository(Aulas::class)->findOneBy([]"num_aula" => $numAula]);
        */

        $gestorEntidades = $gestorFilas->getManager();
        $repoAulas = $gestorEntidades->getRepository(Aulas::class);
        $arrayCriterios = ["num_aula" => $numAula];
        $aula = $repoAulas->findOneBy($arrayCriterios);

        if (!$aula) {
            return new Response("<p style='color:red; font-weight: bold'>NO existe Aula con número $numAula");
        } else {
            // Actualización
            $aula->setCapacidad($capacidad);
            $aula->setDocente($docente);
            $aula->setHardware($hardware);
            $gestorEntidades->flush();

            // Redirección entre endpoints- se pone nombre app
            return $this->redirectToRoute("app_aulas_consultarAulas");
        }
    }
}

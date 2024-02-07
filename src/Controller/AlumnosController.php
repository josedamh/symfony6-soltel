<?php

namespace App\Controller;

use App\Entity\Alumnos;
use App\Entity\Aulas;
use DateTime;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\AlumnosRepository;

/**
 * REANUDAR BBDD
 * - En MySQL: DROP DATABASE soltel_liga;
 * php bin/console doctrine:database:create
 * php bin/console doctrine:migrations:migrate 
 * 
 * LISTADO ENDPOINTS
 * http://127.0.0.1:8000/aulas/23/15/Iván Rodríguez/1
 * http://127.0.0.1:8000/aulas/insertarAulas
 * http://127.0.0.1:8000/alumnos/insertarAlumnos
 * http://127.0.0.1:8000/alumnos/insertar/45612378K/Juan Carlos/22/0/2001-09-16/23
 * http://127.0.0.1:8000/clubes/insertarClubes
 * 
 * SACAR LISTADO DE TABLAS
 * php bin/console dbal:run-sql 'SELECT * FROM alumnos'
 */


#[Route('/alumnos', name: 'app_alumnos')]
class AlumnosController extends AbstractController
{
    #[Route('/insertarAlumnos', name: 'app_alumnos_insertar1')]
    public function index(EntityManagerInterface $gestorEntidades): Response
    {   

        // endpoint de ejemplo: http://127.0.0.1:8000/alumnos/insertarAlumnos
        $alumnos = array(
            "alu1" => array(
                "nif" => "22223333J",
                "nombre" => "Blanca",
                "edad" => 30,
                "sexo" => 1,
                "fechanac" => "1994-01-10",
                "num_aula" => 23
            ),
            "alu2" => array(
                "nif" => "12344321X",
                "nombre" => "Alba",
                "edad" => 28,
                "sexo" => 1,
                "fechanac" => "1996-02-02",
                "num_aula" => 23
            )
        );

        $otrosAlumnos = array(
            "alu1" => array(
                "nif" => "77778888G",
                "nombre" => "José Antonio",
                "edad" => 30,
                "sexo" => 0,
                "fechanac" => "1994-01-10",
                "num_aula" => 22
            ),
            "alu2" => array(
                "nif" => "99998888H",
                "nombre" => "Jairo",
                "edad" => 28,
                "sexo" => 0,
                "fechanac" => "1996-02-02",
                "num_aula" => 22
            )
        );


            foreach ($otrosAlumnos as $registro) {
                    $alumno = new Alumnos();
                    $alumno->setNif($registro['nif']);
                    $alumno->setNombre($registro['nombre']);
                    $alumno->setEdad($registro['edad']);
                    $alumno->setSexo($registro['sexo']);

                    $fechanac = new DateTime($registro['fechanac']);
                    $alumno->setFechaNac($fechanac);


                    // $aula = $gestorEntidades->getRepository(Aulas::class)->findOneBy(['num_aula' => $registro['num_aula']]);
                    // $alumno->setAulasNumAula($aula);
                    
                    $repoAulas = $gestorEntidades->getRepository(Aulas::class);
                    $paramBusqueda = ["num_aula" => $registro['num_aula']];
                    $aula = $repoAulas->findOneBy($paramBusqueda);

                    $alumno->setAulasNumAula($aula);

                    $gestorEntidades->persist($alumno);
                    $gestorEntidades->flush();
            }
            return new Response("<h1>Alumnado insertado</h1>");
        }
        #[Route('/insertar/{nif}/{nombre}/{edad}/{sexo}/{fechanac}/{numAula}', name: 'app_alumnos_insertar2')]
        public function meteAlumno(
            String $nif,
            String $nombre,
            int $edad,
            bool $sexo,
            String $fechanac,
            int $numAula,
            EntityManagerInterface $gestorEntidades
            ): Response {   
    
            // endpoint de ejemplo: http://127.0.0.1:8000/alumnos/insertar/45612378K/Juan Carlos/22/0/2001-09-16/23
    
                        $alumno = new Alumnos();
                        $alumno->setNif($nif);
                        $alumno->setNombre($nombre);
                        $alumno->setEdad($edad);
                        $alumno->setSexo($sexo);
    
                        $fechanac = new DateTime($fechanac);
                        $alumno->setFechaNac($fechanac);
    
                        $aula = $gestorEntidades->getRepository(Aulas::class)
                            ->findOneBy(['num_aula' => $numAula]);

                        $alumno->setAulasNumAula($aula);
    
                        $gestorEntidades->persist($alumno);
                        $gestorEntidades->flush();
                return new Response("<h1>Alumnado insertado</h1>");
            }

            // SELECT con json

            #[Route('/verAlumnos/{aula}/{sexo}', name: 'app_alumnos_ver_alumnos')]
            public function verAlumnos(EntityManagerInterface $gestorEntidades,
            int $aula,
            bool $sexo
            ): JsonResponse{
                // Ejemplo endpoint: http://127.0.0.1:8000/alumnos/verAlumnos/23/1
                $repoAlumnos = $gestorEntidades->getRepository(Alumnos::class);
                $param = ['aulas_num_aula' => $aula, 'sexo' => $sexo];
                $paramOrdenacion = ['nombre' => 'DESC'];
                $filasAlumnos = $repoAlumnos->findBy($param, $paramOrdenacion);

                $json = array();
                foreach ($filasAlumnos as $alumno) {
                    $json[] = array(
                        'nifAlu' => $alumno->getNif(),
                        'nombreAlu' => $alumno->getNombre(),
                        'edadAlu' => $alumno->getEdad()

                    );
                }

                return new JsonResponse($json);

            }

            #[Route('/consultarAlumnos', name: 'app_alumnos_consultar_alumnos')]
            public function consultarAlumnos(ManagerRegistry $gestorDoctrine): Response
            {
                $conexion = $gestorDoctrine->getConnection();
                $alumnos = $conexion
                    ->prepare(" SELECT nif AS dni, nombre, edad, sexo, fechanac, num_aula, docente
                                from alumnos
                                join aulas
                                on num_aula=aulas_num_aula;
                            ")
                    ->executeQuery()
                    ->fetchAllAssociative();

                    /*
                    Para probar correctamente
                    $contenidoAlumnos = json_encode($alumnos);
                    return new Response($contenidoAlumnos);
                    */
                    return $this->render('alumnos/index.html.twig', [
                        'controller_name' => 'Controlador Alumnos',
                        'filasAlumnos' => $alumnos,
                    ]);
                    
            }

            /**
             * @todo crear método en el repositorio para hacer el JOIN
             * 
             */
            
            #[Route('/consultarAlumnosAulas', name: 'app_alumnos_consultar_alumnos_aulas')]
            public function consultarAlumnosAulas(AlumnosRepository $repoAlumno): Response
            {
                $alumnos = $repoAlumno->unirAlumnosAulas();
                
                return $this->render('alumnos/index.html.twig', [
                    'controller_name' => 'Controlador Alumnos',
                    'filasAlumnos' => $alumnos,
                ]);
            }

            #[Route('/consultarAlumnas/{fecha}', name: 'app_alumnos_consultar_alumnas')]
            public function consultarAlumnas(AlumnosRepository $repoAlumno, String $fecha): Response{
                
                // Ejemplo endpoint: http://127.0.0.1:8000/alumnos/consultarAlumnas/1994-02-07
                $alumnas = $repoAlumno->consultarAlumnas($fecha);
                return $this->render('alumnos/index.html.twig', [
                    'controller_name' => 'Controlador Alumnos',
                    'RegistrosAlumnas' => $alumnas,
                ]);
            }
}

    


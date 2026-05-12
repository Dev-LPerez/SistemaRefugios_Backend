<?php
require_once __DIR__ . '/../../familias/service/FamiliaService.php';
require_once __DIR__ . '/../../familias/service/MiembroService.php';

class MotorPriorizacionService
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * RF-04.02: Algoritmo de Puntaje de Prioridad
     * Barema a las familias basándose en sus integrantes y vulnerabilidades
     */
    public function calcularPuntajePrioridad($id_familia)
    {
        $query = "SELECT m.edad, m.vulnerable, m.tipo_vulnerabilidad 
                  FROM miembros m 
                  WHERE m.id_familia = :id_familia";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_familia', $id_familia, PDO::PARAM_INT);
        $stmt->execute();
        $miembros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Puntaje Base
        $puntajeTotal = 10; // Solo por ser familia damnificada

        foreach ($miembros as $miembro) {
            // Bonificación por menores de 5 años
            if (isset($miembro['edad']) && $miembro['edad'] < 5) {
                $puntajeTotal += 5;
            }
            // Bonificación por tercera edad
            if (isset($miembro['edad']) && $miembro['edad'] > 65) {
                $puntajeTotal += 5;
            }
            // Bonificación por vulnerabilidad especial (Embarazo, Discapacidad, etc.)
            if ($miembro['vulnerable'] == 1 || $miembro['vulnerable'] === true) {
                $puntajeTotal += 10;
            }
        }

        // Se guarda o simplemente se devuelve
        return $puntajeTotal;
    }

    /**
     * RF-04.01: Algoritmo "Ración de Supervivencia" (Kits para 3 días)
     * Por persona calculamos: 6 Litros de agua y 4.5 KG de alimentos
     */
    public function calcularRacionSupervivencia($cantidad_miembros)
    {
        $dias = 3;
        $aguaPorPersonaDiaria = 2; // Litros
        $alimentoPorPersonaDiaria = 1.5; // Kilos

        $totalAgua = $cantidad_miembros * $aguaPorPersonaDiaria * $dias;
        $totalAlimentos = $cantidad_miembros * $alimentoPorPersonaDiaria * $dias;

        return [
            "dias_cobertura" => $dias,
            "agua_litros" => $totalAgua,
            "alimentos_kilos" => $totalAlimentos
        ];
    }

    /**
     * RF-04.03: Generador de Listas de Despacho
     * Lista de familias ordenadas por prioridad incluyendo la ración teórica
     */
    public function generarDespachos()
    {
        // Traemos todas las familias y contamos a sus miembros
        $query = "SELECT f.id_familia, f.representante, f.ubicacion_actual, f.refugio_id, 
                         (SELECT COUNT(*) FROM miembros m WHERE m.id_familia = f.id_familia) as cantidad_miembros
                  FROM familias f";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $familias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $listaDespacho = [];

        foreach ($familias as $familia) {
            $cantidad_miembros = $familia['cantidad_miembros'] == 0 ? 1 : $familia['cantidad_miembros'];

            $puntaje = $this->calcularPuntajePrioridad($familia['id_familia']);
            $racion = $this->calcularRacionSupervivencia($cantidad_miembros);

            $listaDespacho[] = [
                "id_familia" => $familia['id_familia'],
                "representante" => $familia['representante'],
                "ubicacion" => $familia['ubicacion_actual'],
                "miembros" => $cantidad_miembros,
                "puntaje_prioridad" => $puntaje,
                "raciones_necesarias" => $racion
            ];
        }

        // Ordenamos el array por puntaje descendente (Mayor prioridad primero)
        usort($listaDespacho, function($a, $b) {
            return $b['puntaje_prioridad'] <=> $a['puntaje_prioridad'];
        });

        return [
            "status" => 200, 
            "message" => "Lista de despacho generada exitosamente.",
            "total_familias" => count($listaDespacho),
            "data" => $listaDespacho
        ];
    }
}

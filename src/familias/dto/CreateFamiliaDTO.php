<?php
class CreateFamiliaDTO
{
    public $representante;
    public $telefono;
    public $direccion;
    public $cantidad_miembros;
    public $prioridad;
    public $id_refugio;
    public $ubicacion_actual;
    public $aceptacion_habeas_data;
    public $cedula;      // Añadido para la validación de duplicidad

    public function __construct($data)
    {
        $this->cedula = $data['cedula'] ?? null;
        $this->representante = $data['representante'] ?? null;
        $this->telefono = $data['telefono'] ?? null;
        $this->direccion = $data['direccion'] ?? null;
        $this->cantidad_miembros = $data['cantidad_miembros'] ?? null;
        $this->prioridad = $data['prioridad'] ?? null;
        
        // Soporte retrocompatible y nuevos campos
        $idRefugio = $data['id_refugio'] ?? $data['refugio_id'] ?? null;
        $this->id_refugio = ($idRefugio === 0 || $idRefugio === '0' || $idRefugio === '') ? null : (int)$idRefugio;
        $this->ubicacion_actual = $data['ubicacion_actual'] ?? 'Vivienda';
        
        // Aceptación habeas data (booleano)
        if (isset($data['aceptacion_habeas_data'])) {
            $this->aceptacion_habeas_data = filter_var($data['aceptacion_habeas_data'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        } else {
            $this->aceptacion_habeas_data = 0;
        }
    }

    public function isValid()
    {
        // En Fase 2 (offline) es mandatorio el DNI para la validación de duplicados y Habeas Data explícito
        return !empty($this->cedula) &&
            !empty($this->representante) &&
            !empty($this->direccion) &&
            isset($this->aceptacion_habeas_data);
    }
}
<?php
class UpdateFamiliaDTO
{
    public $id_familia;
    public $representante;
    public $telefono;
    public $direccion;
    public $cantidad_miembros;
    public $prioridad;
    public $id_refugio;
    public $ubicacion_actual;
    public $aceptacion_habeas_data;
    public $cedula;

    public function __construct($id, $data)
    {
        $this->id_familia = $id;
        $this->cedula = $data['cedula'] ?? null;
        $this->representante = $data['representante'] ?? null;
        $this->telefono = $data['telefono'] ?? null;
        $this->direccion = $data['direccion'] ?? null;
        $this->cantidad_miembros = $data['cantidad_miembros'] ?? null;
        $this->prioridad = $data['prioridad'] ?? null;
        $this->id_refugio = $data['id_refugio'] ?? $data['refugio_id'] ?? null;
        $this->ubicacion_actual = $data['ubicacion_actual'] ?? null;
        
        if (isset($data['aceptacion_habeas_data'])) {
            $this->aceptacion_habeas_data = filter_var($data['aceptacion_habeas_data'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        } else {
            $this->aceptacion_habeas_data = null; // null si no se intenta actualizar
        }
    }

    public function isValid()
    {
        return !empty($this->id_familia) &&
            (!empty($this->representante) || !empty($this->direccion) || !empty($this->cantidad_miembros) || !empty($this->id_refugio) || !empty($this->ubicacion_actual));
    }
}
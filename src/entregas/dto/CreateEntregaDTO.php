<?php
class CreateEntregaDTO
{
    public $estado;
    public $fecha;
    public $id_familia;
    public $recursos = [];

    public function __construct($data)
    {
        $this->estado = $data['estado'] ?? 'Entregado'; // Por defecto lo marcamos como entregado
        $this->fecha = $data['fecha'] ?? date('Y-m-d');
        $this->id_familia = $data['id_familia'] ?? null;
        
        if (isset($data['recursos']) && is_array($data['recursos'])) {
            $this->recursos = $data['recursos'];
        } elseif (isset($data['id_recurso']) && isset($data['cantidad'])) {
            $this->recursos[] = [
                'id_recurso' => $data['id_recurso'],
                'cantidad' => (int) $data['cantidad']
            ];
        }
    }

    public function isValid()
    {
        if (empty($this->id_familia) || empty($this->recursos)) {
            return false;
        }

        foreach ($this->recursos as $recurso) {
            if (empty($recurso['id_recurso']) || !isset($recurso['cantidad']) || (int)$recurso['cantidad'] <= 0) {
                return false;
            }
        }

        return true;
    }
}
<?php
class UpdateMiembroDTO
{
    public $id_persona;
    public $nombre;
    public $edad;
    public $parentezco;
    public $tipo_documento;
    public $numero_documento;
    public $vulnerable;
    public $tipo_vulnerabilidad;
    public $id_familia;

    public function __construct($id, $data)
    {
        $this->id_persona = $id;
        $this->nombre = $data['nombre'] ?? null;
        $this->edad = $data['edad'] ?? null;
        $this->parentezco = $data['parentezco'] ?? null;
        $this->tipo_documento = $data['tipo_documento'] ?? null;
        $this->numero_documento = $data['numero_documento'] ?? null;
        $this->vulnerable = isset($data['vulnerable']) ? (int) $data['vulnerable'] : null;
        $this->tipo_vulnerabilidad = $data['tipo_vulnerabilidad'] ?? null;
        $this->id_familia = $data['id_familia'] ?? null;
    }

    public function isValid()
    {
        return !empty($this->id_persona);
    }
}
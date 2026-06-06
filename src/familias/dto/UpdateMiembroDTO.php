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
    public $es_embarazada;
    public $tiene_discapacidad;
    public $enfermedad_cronica;

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
        $this->es_embarazada = isset($data['es_embarazada']) ? (int) $data['es_embarazada'] : 0;
        $this->tiene_discapacidad = isset($data['tiene_discapacidad']) ? (int) $data['tiene_discapacidad'] : 0;
        $this->enfermedad_cronica = isset($data['enfermedad_cronica']) ? (int) $data['enfermedad_cronica'] : 0;
    }

    public function isValid()
    {
        return !empty($this->id_persona);
    }
}
<?php
/**
 * Programa — el MODELO de la v1: la clase que representa una fila de la tabla
 * `programa` como un objeto.
 *
 * Estilo clásico de P.O.O. (encapsulamiento):
 *   - las propiedades son PRIVADAS: nadie por fuera las toca directamente;
 *   - se LEEN con getters;
 *   - se CAMBIAN con setters;
 *   - `id` NO tiene setter: es la llave primaria — se fija al
 *     crear el objeto y no cambia nunca.
 *
 * Lo que este modelo NO tiene, y es a propósito: la columna `activo`. La
 * usa el repositorio para el borrado lógico, pero no es un dato del
 * programa — es cómo la base recuerda que ya no está. Si estuviera
 * aquí, alguien terminaría mandándola en un PUT.
 */

// Modo estricto de tipos (ver explicación completa en index.php):
declare(strict_types=1);

class Programa
{
    private int $id;  // El código del programa, como `10101`.
    private string $nombre;
    private string $tipo;
    private string $nivel;
    private string $fecha_creacion;  // Es TEXTO en el esquema dado, no una fecha.
    private string $numero_cohortes;
    private string $cant_graduados;
    private string $fecha_actualizacion;
    private string $ciudad;
    private int $facultad;  // Un número sin clave foránea: la tabla facultad no existe en este módulo.
    private ?string $fecha_cierre;  // El ÚNICO opcional: un programa abierto no tiene fecha de cierre.

    public function __construct(
        int $id,
        string $nombre,
        string $tipo,
        string $nivel,
        string $fecha_creacion,
        string $numero_cohortes,
        string $cant_graduados,
        string $fecha_actualizacion,
        string $ciudad,
        int $facultad,
        ?string $fecha_cierre,
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->nivel = $nivel;
        $this->fecha_creacion = $fecha_creacion;
        $this->numero_cohortes = $numero_cohortes;
        $this->cant_graduados = $cant_graduados;
        $this->fecha_actualizacion = $fecha_actualizacion;
        $this->ciudad = $ciudad;
        $this->facultad = $facultad;
        $this->fecha_cierre = $fecha_cierre;
    }

    // ------------------------------------------------------------------
    // GETTERS — para LEER cada propiedad desde afuera
    // ------------------------------------------------------------------

    public function getId(): int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getNivel(): string
    {
        return $this->nivel;
    }

    public function getFechaCreacion(): string
    {
        return $this->fecha_creacion;
    }

    public function getNumeroCohortes(): string
    {
        return $this->numero_cohortes;
    }

    public function getCantGraduados(): string
    {
        return $this->cant_graduados;
    }

    public function getFechaActualizacion(): string
    {
        return $this->fecha_actualizacion;
    }

    public function getCiudad(): string
    {
        return $this->ciudad;
    }

    public function getFacultad(): int
    {
        return $this->facultad;
    }

    public function getFechaCierre(): ?string
    {
        return $this->fecha_cierre;
    }

    // ------------------------------------------------------------------
    // SETTERS — solo para lo que puede cambiar.
    // La llave no tiene: identificar y modificar son cosas distintas.
    // ------------------------------------------------------------------

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function setNivel(string $nivel): void
    {
        $this->nivel = $nivel;
    }

    public function setFechaCreacion(string $fecha_creacion): void
    {
        $this->fecha_creacion = $fecha_creacion;
    }

    public function setNumeroCohortes(string $numero_cohortes): void
    {
        $this->numero_cohortes = $numero_cohortes;
    }

    public function setCantGraduados(string $cant_graduados): void
    {
        $this->cant_graduados = $cant_graduados;
    }

    public function setFechaActualizacion(string $fecha_actualizacion): void
    {
        $this->fecha_actualizacion = $fecha_actualizacion;
    }

    public function setCiudad(string $ciudad): void
    {
        $this->ciudad = $ciudad;
    }

    public function setFacultad(int $facultad): void
    {
        $this->facultad = $facultad;
    }

    public function setFechaCierre(?string $fecha_cierre): void
    {
        $this->fecha_cierre = $fecha_cierre;
    }

    // ------------------------------------------------------------------
    // Conversión para la respuesta JSON
    // ------------------------------------------------------------------

    /**
     * Devuelve el programa como array (columna => valor), listo
     * para que json_encode lo convierta en JSON. Hace falta porque las
     * propiedades son privadas: json_encode no las ve.
     */
    public function toArray(): array
    {
        return [
            'id'                     => $this->id,
            'nombre'                 => $this->nombre,
            'tipo'                   => $this->tipo,
            'nivel'                  => $this->nivel,
            'fecha_creacion'         => $this->fecha_creacion,
            'numero_cohortes'        => $this->numero_cohortes,
            'cant_graduados'         => $this->cant_graduados,
            'fecha_actualizacion'    => $this->fecha_actualizacion,
            'ciudad'                 => $this->ciudad,
            'facultad'               => $this->facultad,
            'fecha_cierre'           => $this->fecha_cierre,
        ];
    }
}

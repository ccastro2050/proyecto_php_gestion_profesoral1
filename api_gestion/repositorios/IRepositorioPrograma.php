<?php
/**
 * IRepositorioPrograma — el CONTRATO de la capa de datos.
 *
 * Una interface nativa de PHP define QUÉ operaciones existen sobre
 * `programa`, sin decir CÓMO ni CONTRA QUÉ motor. Cualquier clase con
 * `implements IRepositorioPrograma` puede ocupar este lugar: el MariaDB real
 * de la v1 o el falso en memoria de las pruebas (polimorfismo).
 *
 * El servicio depende de ESTA interfaz, nunca de una clase concreta
 * (inversión de dependencias — la D de SOLID).
 *
 * Las lecturas devuelven objetos del MODELO, no arrays: la capa de datos
 * entrega el dato ya tipado y limpio.
 */

// Modo estricto de tipos (ver explicación completa en index.php):
declare(strict_types=1);

require_once __DIR__ . '/../modelos/Programa.php';

interface IRepositorioPrograma
{
    /**
     * Hasta $limite filas ACTIVAS, ordenadas por llave.
     * @return Programa[]
     */
    public function obtenerTodos(int $limite): array;

    /** La fila ACTIVA con esa llave, o null si no existe o está inactiva. */
    public function obtenerPorClave(int $id): ?Programa;

    /** Inserta. true = insertado. */
    public function crear(Programa $programa): bool;

    /**
     * Escribe los campos de $datos (los usan PUT y PATCH). Va como array
     * porque un PATCH puede traer SOLO algunos campos.
     * Devuelve filas afectadas (0 = la llave no existe o está inactiva).
     */
    public function actualizar(int $id, array $datos): int;

    /**
     * Borrado LÓGICO: marca `activo = FALSE`. La fila NO se va de la base.
     * Devuelve filas afectadas (0 = no existía, o ya estaba inactiva).
     */
    public function eliminar(int $id): int;
}

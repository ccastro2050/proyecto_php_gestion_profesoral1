<?php
/**
 * prueba_capas.php — el servicio funciona con un repositorio FALSO en
 * memoria que implementa IRepositorioPrograma, **sin MariaDB corriendo**.
 *
 * Si esto pasa, las capas quedaron bien cortadas: polimorfismo (otra clase
 * en el mismo hueco) e inversión de dependencias (el servicio depende de la
 * interfaz, no de la clase concreta).
 *
 * Ejecutar:  php pruebas/prueba_capas.php
 * O dentro de Docker, sin apagar nada:
 *   docker compose exec api-gestion php pruebas/prueba_capas.php
 */

// Modo estricto de tipos (ver explicación completa en index.php):
declare(strict_types=1);

// Este require arrastra, por sus propios require_once, la interfaz del
// repositorio, el modelo y las excepciones:
require_once __DIR__ . '/../servicios/ServicioPrograma.php';

/**
 * El REPOSITORIO FALSO: cumple el mismo contrato que el de MariaDB, pero
 * guarda los objetos en un array en memoria — cero SQL, cero red. Como el
 * servicio depende de la INTERFAZ, no nota la diferencia.
 *
 * Fíjese en que el borrado también es LÓGICO aquí: se lleva una lista de
 * llaves retiradas. Si el falso borrara de verdad, la prueba pasaría con un
 * comportamiento que el sistema real no tiene, y eso es peor que no probar.
 */
class RepositorioFalsoEnMemoria implements IRepositorioPrograma
{
    /** El "almacén": la llave como índice y el objeto como valor. */
    private array $datos = [];

    /** Las llaves retiradas: el equivalente en memoria de `activo = FALSE`. */
    private array $inactivas = [];

    private function estaActiva(int $clave): bool
    {
        return isset($this->datos[$clave]) && !in_array($clave, $this->inactivas, true);
    }

    public function obtenerTodos(int $limite): array
    {
        $activas = [];
        foreach ($this->datos as $clave => $fila) {
            if ($this->estaActiva($clave)) {
                $activas[] = $fila;
            }
        }
        // array_slice corta los primeros $limite (como el LIMIT del SQL):
        return array_slice($activas, 0, $limite);
    }

    public function obtenerPorClave(int $clave): ?Programa
    {
        return $this->estaActiva($clave) ? $this->datos[$clave] : null;
    }

    public function crear(Programa $entidad): bool
    {
        $this->datos[$entidad->getId()] = $entidad;
        return true;
    }

    public function actualizar(int $clave, array $datos): int
    {
        if (!$this->estaActiva($clave)) {
            return 0;
        }
        // Se escriben SOLO los campos que llegaron (igual que el UPDATE
        // dinámico del repositorio real), usando los SETTERS del modelo:
        $fila = $this->datos[$clave];
        if (array_key_exists('nombre', $datos)) {
            $fila->setNombre($datos['nombre']);
        }
        if (array_key_exists('tipo', $datos)) {
            $fila->setTipo($datos['tipo']);
        }
        if (array_key_exists('nivel', $datos)) {
            $fila->setNivel($datos['nivel']);
        }
        if (array_key_exists('fecha_creacion', $datos)) {
            $fila->setFechaCreacion($datos['fecha_creacion']);
        }
        if (array_key_exists('numero_cohortes', $datos)) {
            $fila->setNumeroCohortes($datos['numero_cohortes']);
        }
        if (array_key_exists('cant_graduados', $datos)) {
            $fila->setCantGraduados($datos['cant_graduados']);
        }
        if (array_key_exists('fecha_actualizacion', $datos)) {
            $fila->setFechaActualizacion($datos['fecha_actualizacion']);
        }
        if (array_key_exists('ciudad', $datos)) {
            $fila->setCiudad($datos['ciudad']);
        }
        if (array_key_exists('facultad', $datos)) {
            $fila->setFacultad($datos['facultad']);
        }
        if (array_key_exists('fecha_cierre', $datos)) {
            $fila->setFechaCierre($datos['fecha_cierre']);
        }
        return 1;
    }

    public function eliminar(int $clave): int
    {
        if (!$this->estaActiva($clave)) {
            return 0;
        }
        $this->inactivas[] = $clave;   // la fila NO se va del array
        return 1;
    }
}

// ----------------------------------------------------------------------
// La prueba: el MISMO ServicioPrograma, con otro repositorio (polimorfismo)
// ----------------------------------------------------------------------
$servicio = new ServicioPrograma(new RepositorioFalsoEnMemoria());

/** Mini-verificador: si la condición es falsa, reporta y sale con error. */
function verificar(bool $condicion, string $descripcion): void
{
    if (!$condicion) {
        // STDERR es la salida de errores; exit(1) = terminar "mal"
        // (los scripts que salen con 0 pasaron, con != 0 fallaron):
        fwrite(STDERR, "FALLÓ: $descripcion\n");
        exit(1);
    }
    echo "[OK] $descripcion\n";
}

$clave = 9001;

// El ciclo completo contra el repositorio falso. Note que las lecturas
// devuelven OBJETOS del modelo: se pregunta con los getters, no con llaves
// de array.
$servicio->crear([
            'id' => 9001,
            'nombre' => 'Ingenieria de Sistemas',
            'tipo' => 'Programa Academico',
            'nivel' => 'Pregrado',
            'fecha_creacion' => '2005-01-15',
            'numero_cohortes' => '40',
            'cant_graduados' => '1250',
            'fecha_actualizacion' => '2026-01-30',
            'ciudad' => 'Medellin',
            'facultad' => 104,
            'fecha_cierre' => null,
]);
verificar($servicio->listar(10)[0]->getId() === $clave, 'crear y listar');
verificar($servicio->obtener($clave) instanceof Programa,           'obtener por llave');

verificar($servicio->actualizar($clave, ['nombre' => 'Cambiado']) === 1,
          'actualizar un solo campo');
verificar($servicio->obtener($clave)->getNombre() === 'Cambiado',
          'el campo quedó con el valor nuevo');

verificar($servicio->eliminar($clave) === 1,   'retirar (borrado lógico)');
verificar($servicio->listar(10) === [],        'y ya no aparece en el listado');

// Las excepciones de negocio también funcionan sin base de datos:
try {
    $servicio->obtener($clave);
    verificar(false, 'obtener una fila retirada debió lanzar NoEncontradoExcepcion');
} catch (NoEncontradoExcepcion) {
    echo "[OK] obtener una fila retirada lanza NoEncontradoExcepcion\n";
}

try {
    $servicio->eliminar($clave);
    verificar(false, 'retirar dos veces debió lanzar NoEncontradoExcepcion');
} catch (NoEncontradoExcepcion) {
    echo "[OK] retirar dos veces lanza NoEncontradoExcepcion (el segundo DELETE es 404)\n";
}

try {
    $servicio->actualizar($clave, []);
    verificar(false, 'un cuerpo vacío debió lanzar InvalidArgumentException');
} catch (InvalidArgumentException) {
    echo "[OK] un cuerpo vacío lanza InvalidArgumentException (400, no 422)\n";
}

try {
    $servicio->listar(0);
    verificar(false, 'limite = 0 debió lanzar InvalidArgumentException');
} catch (InvalidArgumentException) {
    echo "[OK] limite = 0 lanza InvalidArgumentException\n";
}

echo "\n=== Prueba de capas completada CON ÉXITO ===\n";
echo "El servicio corrió entero sin MariaDB. Ninguna capa de negocio sabe\n";
echo "qué motor hay detrás, y eso es lo que se acaba de comprobar.\n";

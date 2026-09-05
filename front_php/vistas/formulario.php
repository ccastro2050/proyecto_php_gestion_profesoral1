<?php
/**
 * El formulario de una ficha: sirve para agregar y para editar.
 *
 * La diferencia entre los dos usos está en $editando, y se ve en dos sitios:
 * la llave va de solo lectura al editar, y aparecen DOS botones de guardar
 * en vez de uno.
 */
?>
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
  <div>
    <h1 class="h3 mb-1"><?= $editando ? 'Editar la ficha' : 'Agregar el programa' ?></h1>
    <p class="text-body-secondary mb-0">
      <?php if ($editando): ?>
        La llave identifica la ficha y no se cambia. Si está mal, se agrega
        otra y se retira ésta.
      <?php else: ?>
        La llave la escribe usted y no se podrá cambiar después.
      <?php endif; ?>
    </p>
  </div>
  <a class="btn btn-outline-secondary" href="/programas">Volver al listado</a>
</div>

<div class="card shadow-sm" style="max-width: 46rem;">
  <div class="card-body p-4">
    <form method="post">

      <div class="mb-3">
        <label class="form-label" for="id">Código</label>
        <input class="form-control font-monospace" type="number" id="id" name="id" min="0"
               value="<?= htmlspecialchars((string) ($ficha['id'] ?? '')) ?>"
               <?= $editando ? 'readonly' : 'required autofocus' ?>>
        <div class="form-text">El código del programa, como <code>10101</code>.</div>
      </div>

      <div class="mb-3">
        <label class="form-label" for="nombre">Nombre</label>
        <textarea class="form-control" id="nombre" name="nombre" rows="3"
                maxlength="150"><?= htmlspecialchars((string) ($ficha['nombre'] ?? '')) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label" for="tipo">Tipo</label>
        <input class="form-control" type="text" id="tipo" name="tipo"
               maxlength="45"
               value="<?= htmlspecialchars((string) ($ficha['tipo'] ?? '')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label" for="nivel">Nivel</label>
        <input class="form-control" type="text" id="nivel" name="nivel"
               maxlength="45"
               value="<?= htmlspecialchars((string) ($ficha['nivel'] ?? '')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label" for="fecha_creacion">Fecha de creación</label>
        <input class="form-control" type="text" id="fecha_creacion" name="fecha_creacion"
               maxlength="45"
               value="<?= htmlspecialchars((string) ($ficha['fecha_creacion'] ?? '')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label" for="numero_cohortes">Número de cohortes</label>
        <input class="form-control" type="text" id="numero_cohortes" name="numero_cohortes"
               maxlength="45"
               value="<?= htmlspecialchars((string) ($ficha['numero_cohortes'] ?? '')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label" for="cant_graduados">Cantidad de graduados</label>
        <input class="form-control" type="text" id="cant_graduados" name="cant_graduados"
               maxlength="45"
               value="<?= htmlspecialchars((string) ($ficha['cant_graduados'] ?? '')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label" for="fecha_actualizacion">Fecha de actualización</label>
        <input class="form-control" type="text" id="fecha_actualizacion" name="fecha_actualizacion"
               maxlength="45"
               value="<?= htmlspecialchars((string) ($ficha['fecha_actualizacion'] ?? '')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label" for="ciudad">Ciudad</label>
        <input class="form-control" type="text" id="ciudad" name="ciudad"
               maxlength="45"
               value="<?= htmlspecialchars((string) ($ficha['ciudad'] ?? '')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label" for="facultad">Facultad</label>
        <input class="form-control" type="number" id="facultad" name="facultad" min="0"
               value="<?= htmlspecialchars((string) ($ficha['facultad'] ?? '')) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label" for="fecha_cierre">Fecha de cierre <span class="text-body-secondary fw-normal">(opcional)</span></label>
        <input class="form-control" type="text" id="fecha_cierre" name="fecha_cierre"
               maxlength="45"
               value="<?= htmlspecialchars((string) ($ficha['fecha_cierre'] ?? '')) ?>">
        <div class="form-text">Déjela en blanco si el programa sigue abierto.</div>
      </div>

      <hr class="my-4">

      <?php /* ==============================================================
           LOS DOS BOTONES, QUE NO HACEN LO MISMO

             · «Guardar la ficha completa» manda todo, así que un dato
               obligatorio en blanco se rechaza.
             · «Guardar solo lo que cambié» manda únicamente lo diligenciado,
               así que el mismo formulario a medio llenar sí se guarda.

           El mismo formulario, dos comportamientos, y la diferencia no la
           decide ningún `if` de negocio: la decide QUÉ SE ENVÍA.
           ============================================================== */ ?>
      <?php if ($editando): ?>
        <div class="d-flex flex-wrap gap-2">
          <button class="btn btn-primary" type="submit" name="verbo" value="completa">
            Guardar la ficha completa
          </button>
          <button class="btn btn-outline-primary" type="submit" name="verbo" value="parcial">
            Guardar solo lo que cambié
          </button>
        </div>
        <div class="form-text mt-3">
          <strong>«La ficha completa»</strong> exige que todos los datos
          obligatorios estén diligenciados. <strong>«Solo lo que cambié»</strong>
          guarda lo que usted escribió y deja lo demás como estaba.
        </div>
      <?php else: ?>
        <button class="btn btn-primary" type="submit">Agregar</button>
      <?php endif; ?>

    </form>
  </div>
</div>

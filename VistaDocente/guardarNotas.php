<?php
require_once('../Includes/Connection.php');

// Recibir los datos del frontend (nota.php)
$data = json_decode(file_get_contents('php://input'), true);

$iddoc     = $data['iddoc']     ?? null;
$idalum    = $data['idalum']    ?? null;
$idasig    = $data['idasig']    ?? null;
$bimestre  = $data['bimestre']  ?? null;
$ideva     = $data['ideva']     ?? null;
$notaValue = $data['nota']      ?? null;

// Validación rápida
if (!$iddoc || !$idalum || !$idasig || !$ideva || $notaValue === null) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos obligatorios.']);
    exit;
}

// 1) Obtener el aula del alumno
$resAula = mysqli_query($conexion, "
    SELECT aula 
    FROM alumnos 
    WHERE idalum = '$idalum'
");
if (!$resAula || mysqli_num_rows($resAula) === 0) {
    echo json_encode(['success' => false, 'error' => 'No se encontró el aula del alumno.']);
    exit;
}
$idaula = mysqli_fetch_assoc($resAula)['aula'];

// 2) Obtener nombre del curso (tabla `asignaturas`, columna `nombre`)
$nombreCurso = "Curso desconocido";
$resCurso = mysqli_query($conexion, "
    SELECT nombre 
    FROM asignaturas 
    WHERE idasig = '$idasig'
");
if ($resCurso && mysqli_num_rows($resCurso) > 0) {
    $nombreCurso = mysqli_fetch_assoc($resCurso)['nombre'];
}

// 3) Obtener nombre de la evaluación (tabla `evaluaciones`, columna `evaluacion`)
$nombreEval = "Evaluación desconocida";
$resEval = mysqli_query($conexion, "
    SELECT evaluacion 
    FROM evaluaciones 
    WHERE ideva = '$ideva'
");
if ($resEval && mysqli_num_rows($resEval) > 0) {
    $nombreEval = mysqli_fetch_assoc($resEval)['evaluacion'];
}

// 4) Revisar si ya existe la nota
$checkQuery = mysqli_query($conexion, "
    SELECT nota 
    FROM evaluacionnotas 
    WHERE iddoc = '$iddoc' 
      AND idalumn = '$idalum' 
      AND evalua  = '$ideva'
");

if (mysqli_num_rows($checkQuery) > 0) {
    // Ya existe: actualizar nota si cambió
    $notaAnterior = mysqli_fetch_assoc($checkQuery)['nota'];
    if ($notaAnterior != $notaValue) {
        $query = mysqli_query($conexion, "
            UPDATE evaluacionnotas 
               SET nota = '$notaValue' 
             WHERE iddoc   = '$iddoc' 
               AND idalumn = '$idalum' 
               AND evalua  = '$ideva'
        ");
        // Inserta notificación
        $mensaje = "Tu nota de \"$nombreEval\" en \"$nombreCurso\" ha sido actualizada.";
        mysqli_query($conexion, "
            INSERT INTO notificaciones (id_alumno, id_docente, tipo, mensaje)
            VALUES ('$idalum', '$iddoc', 'nota_subida', '$mensaje')
        ");
    } else {
        // No hubo cambio, pero no es error
        $query = true;
    }
} else {
    // No existía: inserta nueva nota
    $query = mysqli_query($conexion, "
        INSERT INTO evaluacionnotas (iddoc, idaula, idalumn, evalua, nota) 
        VALUES ('$iddoc', '$idaula', '$idalum', '$ideva', '$notaValue')
    ");
    // Inserta notificación
    $mensaje = "Se ha registrado tu nota de \"$nombreEval\" en \"$nombreCurso\".";
    mysqli_query($conexion, "
        INSERT INTO notificaciones (id_alumno, id_docente, tipo, mensaje)
        VALUES ('$idalum', '$iddoc', 'nota_subida', '$mensaje')
    ");
}

// 5) Comprobar errores de ejecución
if (!$query) {
    echo json_encode([
        'success' => false,
        'error'   => 'Error en consulta: ' . mysqli_error($conexion)
    ]);
    exit;
}

// 6) Finalmente, devolución al front
echo json_encode(['success' => true]);
?>

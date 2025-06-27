<?php
session_start();
require_once '../includes/conexion.php';

$db = new Database();
$conn = $db->connect();

$correo = $_SESSION['user'] ?? null;
if (!$correo) {
    die("No hay sesión activa.");
}

$id_vacante = $_GET['id'] ?? null;
if (!$id_vacante) {
    die("ID de vacante no proporcionado.");
}

// Obtener datos de la vacante
$stmt = $conn->prepare("SELECT * FROM VACANTE WHERE ID = :id");
$stmt->bindValue(':id', $id_vacante, PDO::PARAM_INT);
$stmt->execute();
$vacante = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$vacante) die("Vacante no encontrada.");

// Escolaridad actual
$stmt = $conn->prepare("SELECT E.NIVEL FROM VACANTE_ESCOLARIDAD VE JOIN ESCOLARIDAD E ON VE.ID_ESCOLARIDAD = E.ID WHERE VE.ID_VACANTE = :id");
$stmt->execute([':id' => $id_vacante]);
$escolaridadActual = $stmt->fetchColumn();

// Títulos actuales
$titulosActuales = [];
$stmt = $conn->prepare("SELECT ID_TITULO FROM VACANTE_TITULO WHERE ID_VACANTE = :id");
$stmt->execute([':id' => $id_vacante]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $titulosActuales[] = $row['ID_TITULO'];
}

// Habilidades actuales
$habilidadesActuales = [];
$stmt = $conn->prepare("SELECT H.NOMBRE FROM VACANTE_HABILIDAD VH JOIN HABILIDAD H ON VH.ID_HABILIDAD = H.ID WHERE VH.ID_VACANTE = :id");
$stmt->execute([':id' => $id_vacante]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $habilidadesActuales[] = $row['NOMBRE'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Vacante</title>
    <link rel="stylesheet" href="../estilo/VacNueva.css" type="text/css">
</head>
<body>
<header>
    <iframe class="header" src="../bases/nav-I-Empresa.html"></iframe>
</header>

<div class="form-container">
    <h2>Editar Vacante</h2>
    <form action="../includes/actualizarVacante.php" method="post">
        <input type="hidden" name="id" value="<?= $id_vacante ?>">

        <label for="puesto">Puesto a solicitar:</label>
        <input type="text" id="puesto" name="puesto" maxlength="50" required value="<?= htmlspecialchars($vacante['PUESTO']) ?>">

        <label for="salario">Salario:</label>
        <input type="number" step="0.01" id="salario" name="salario" required value="<?= $vacante['SALARIO'] ?>">

        <label for="modalidad">Modalidad:</label>
        <select id="modalidad" name="modalidad" required>
            <?php foreach (['Presencial', 'Distancia', 'Mixto'] as $op): ?>
                <option value="<?= $op ?>" <?= $vacante['MODALIDAD'] === $op ? 'selected' : '' ?>><?= $op ?></option>
            <?php endforeach; ?>
        </select>

        <label for="ubicacion">Ubicación:</label>
        <input type="text" id="ubicacion" name="ubicacion" maxlength="200" required value="<?= htmlspecialchars($vacante['UBICACION']) ?>">

        <label for="duracion">Duración del contrato (meses):</label>
        <input type="number" id="duracion" name="duracion" max="12" required value="<?= $vacante['DURACION'] ?>">

        <label for="escolaridad">Escolaridad:</label>
        <select id="escolaridad" name="escolaridad" required>
            <?php
            $niveles = ['Secundaria', 'Preparatoria', 'Licenciatura', 'Maestría', 'Doctorado'];
            foreach ($niveles as $nivel) {
                $selected = $nivel === $escolaridadActual ? 'selected' : '';
                echo "<option value=\"$nivel\" $selected>$nivel</option>";
            }
            ?>
        </select>

        <label for="titulos">Títulos requeridos:</label>
        <select id="titulos" name="titulos[]" multiple required>
            <?php
            $titulos = [
                1 => "Ingeniero de Datos",
                2 => "Analista de Ciberseguridad",
                3 => "Desarrollador de Software",
                4 => "Gestor BD",
                5 => "IA y Machine Learning"
            ];
            foreach ($titulos as $id => $nombre) {
                $selected = in_array($id, $titulosActuales) ? 'selected' : '';
                echo "<option value=\"$id\" $selected>$nombre</option>";
            }
            ?>
        </select>

        <fieldset class="checkbox-group">
            <legend>Habilidades solicitadas:</legend>
            <div class="checkboxes">
                <?php
                $habilidades = [
                    "Comunicación Efectiva", "Trabajo en Equipo", "Pensamiento Crítico", "Adaptabilidad",
                    "Resolución de Problemas", "Liderazgo", "Inteligencia Emocional", "Gestión de Tiempo",
                    "Empatía", "Creatividad", "HTML", "PHP", "CSS", "SQL", "Java", "C", "Python",
                    "Ingeniero de Datos", "Analista de Ciberseguridad", "Desarrollador de Software", "Gestor BD", "IA y Machine Learning"
                ];
                foreach ($habilidades as $hab) {
                    $checked = in_array($hab, $habilidadesActuales) ? 'checked' : '';
                    echo "<label><input type=\"checkbox\" name=\"habilidades[]\" value=\"$hab\" $checked> $hab</label>";
                }
                ?>
            </div>
        </fieldset>

        <button type="submit">Actualizar</button>
    </form>
</div>

<footer>
    <iframe class="footer" src="../bases/Nav-Final.html"></iframe>
</footer>
</body>
</html>


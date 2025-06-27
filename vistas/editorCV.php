<?php
session_start();
require_once '../includes/conexion.php';

$correo = $_SESSION['user'] ?? '';

// Variables para mostrar en el formulario
$nombre = '';
$cv_titulo = '';
$nivel_esc = '';
$habilidades_blandas = [];
$habilidades_duras = [];

try {
    $db = new Database();
    $conn = $db->connect();

    // Obtener ID y nombre del usuario desde el correo
    $stmt = $conn->prepare("SELECT ID, NOMBRE FROM USUARIO WHERE CORREO = :correo AND TIPO_USUARIO = 1");
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $id_usuario = $usuario['ID'];
        $nombre = $usuario['NOMBRE'];

        // Buscar el CV del usuario
        $stmt = $conn->prepare("SELECT * FROM CV WHERE ID_CANDIDATO = :id");
        $stmt->bindParam(':id', $id_usuario);
        $stmt->execute();
        $cv = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cv) {
            $cv_titulo = $cv['TITULO'];

            // Obtener escolaridad
            $stmt = $conn->prepare("SELECT ESCOLARIDAD.NIVEL 
                                    FROM CV_ESCOLARIDAD 
                                    JOIN ESCOLARIDAD ON ESCOLARIDAD.ID = CV_ESCOLARIDAD.ID_ESCOLARIDAD 
                                    WHERE ID_CV = :id_cv");
            $stmt->bindParam(':id_cv', $cv['ID']);
            $stmt->execute();
            $esc = $stmt->fetch(PDO::FETCH_ASSOC);
            $nivel_esc = $esc['NIVEL'] ?? '';

            // Obtener habilidades
            $stmt = $conn->prepare("SELECT HABILIDAD.NOMBRE, HABILIDAD.TIPO 
                                    FROM CV_HABILIDAD 
                                    JOIN HABILIDAD ON HABILIDAD.ID = CV_HABILIDAD.ID_HABILIDAD 
                                    WHERE ID_CV = :id_cv");
            $stmt->bindParam(':id_cv', $cv['ID']);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['TIPO'] === 'BLANDA') {
                    $habilidades_blandas[] = $row['NOMBRE'];
                } else {
                    $habilidades_duras[] = $row['NOMBRE'];
                }
            }
        }
    }
} catch (PDOException $e) {
    // Manejo de errores
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editor de Currículum Vitae</title>
    <link rel="stylesheet" href="../estilo/cv.css" type="text/css">
</head>
<body>
<div class="arriba">
    <iframe class="header" src="../bases/nav-I-User.html"></iframe>
</div>

<div class="contenedor">
    <h1>Editor de Currículum Vitae</h1>
    <p>Llena los campos correspondientes y conviértete en un candidato para las empresas que trabajan con LUMINAR</p>

    <div class="contenedorCV">
        <form name="curriculum" action="../includes/cv.php" method="post">
            <div class="inputs">
                <label for="nom">Nombre:</label>
                <input type="text" id="nom" name="nom" readonly value="<?php echo htmlspecialchars($nombre); ?>">
            </div>
            <div class="inputs">
                <label for="correo">Correo electrónico:</label>
                <input type="text" id="correo" name="correo" readonly value="<?php echo htmlspecialchars($correo); ?>">
            </div>

            <!-- Escolaridad -->
            <div class="escolar">
                <label for="escolaridad">Escolaridad:</label>
                <select id="escolaridad" name="escolaridad" required>
                    <option value="">-- Seleccione un nivel --</option>
                    <?php
                    $niveles = ["Secundaria", "Preparatoria", "Licenciatura", "Maestría", "Doctorado"];
                    foreach ($niveles as $nivel) {
                        $selected = ($nivel_esc === $nivel) ? "selected" : "";
                        echo "<option value=\"$nivel\" $selected>$nivel</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Habilidades blandas -->
            <div class="radios" id="soft">
                <fieldset>
                    <legend>Habilidades blandas:</legend>
                    <div class="soft-columns">
                        <div class="s1">
                            <?php
                            $softs1 = ["Comunicación Efectiva", "Trabajo en Equipo", "Pensamiento Crítico", "Adaptabilidad", "Resolución de Problemas"];
                            foreach ($softs1 as $index => $nombreHabilidad) {
                                $id = "H" . ($index + 1);
                                $checked = in_array($nombreHabilidad, $habilidades_blandas) ? "checked" : "";
                                echo "<div>
                                            <input type='checkbox' id='$id' name='softS[]' value='$nombreHabilidad' $checked>
                                            <label for='$id'>$nombreHabilidad</label>
                                          </div>";
                            }
                            ?>
                        </div>
                        <div class="s2">
                            <?php
                            $softs2 = ["Liderazgo", "Inteligencia Emocional", "Gestión de Tiempo", "Empatía", "Creatividad"];
                            foreach ($softs2 as $index => $nombreHabilidad) {
                                $id = "H" . ($index + 6);
                                $checked = in_array($nombreHabilidad, $habilidades_blandas) ? "checked" : "";
                                echo "<div>
                                            <input type='checkbox' id='$id' name='softS[]' value='$nombreHabilidad' $checked>
                                            <label for='$id'>$nombreHabilidad</label>
                                          </div>";
                            }
                            ?>
                        </div>
                    </div>
                </fieldset>
            </div>

            <!-- Habilidades duras -->
            <div class="radios">
                <fieldset>
                    <legend>Habilidades duras:</legend>
                    <?php
                    $hards = ["HTML", "CSS", "PHP", "SQL", "Java", "C", "Python"];
                    foreach ($hards as $i => $nombreHabilidad) {
                        $id = "H" . ($i + 11);
                        $checked = in_array($nombreHabilidad, $habilidades_duras) ? "checked" : "";
                        echo "<div>
                                    <input type='checkbox' id='$id' name='hardS[]' value='$nombreHabilidad' $checked>
                                    <label for='$id'>$nombreHabilidad</label>
                                  </div>";
                    }
                    ?>
                </fieldset>
            </div>

            <!-- Título obtenido -->
            <div class="radios">
                <fieldset>
                    <legend>Título o reconocimiento obtenido:</legend>
                    <?php
                    $titulos = [
                        "Ingeniero de Datos",
                        "Analista de Ciberseguridad",
                        "Desarrollador de Software",
                        "Gestor BD",
                        "IA y Machine Learning"
                    ];
                    foreach ($titulos as $i => $nombreTitulo) {
                        $id = "H" . ($i + 18);
                        $checked = ($cv_titulo === $nombreTitulo) ? "checked" : "";
                        echo "<div>
                                    <input type='radio' id='$id' name='title' value='$nombreTitulo' $checked required>
                                    <label for='$id'>$nombreTitulo</label>
                                  </div>";
                    }
                    ?>
                </fieldset>
            </div>

            <input type="submit" name="modificar" value="Modificar">
        </form>
    </div>
</div>

<div class="abajo">
    <iframe class="footer" src="../bases/nav-F-User.html"></iframe>
</div>
</body>
</html>

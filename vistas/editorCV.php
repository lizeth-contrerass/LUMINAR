<?php
session_start();

// Simulación de sesión (elimina estas líneas en producción)
$_SESSION['id_usuario'] = 1;
$_SESSION['nombre'] = "Marin";
$_SESSION['correo'] = "maringalvand@gmail.com";

$nombre = $_SESSION['nombre'] ?? '';
$correo = $_SESSION['correo'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor de Currículum Vitae</title>
    <link rel="stylesheet" href="../estilo/cv.css" type="text/css">
</head>
<body>
    <div class="arriba">
        <iframe class="header" src="../bases/nav-I-User.html"></iframe>
    </div>

    <div class="contenedor">
        <h1>Editor de Currículum Vitae</h1>
        <p>
            Llena los campos correspondientes y conviértete en un candidato 
            para las empresas que trabajan con LUMINAR
        </p>
        <div class="contenedorCV">
            <form name="curriculum" action="../includes/cv.php" method="post" enctype="application/x-www-form-urlencoded">
                <div class="inputs">
                    <label for="nom">Nombre:</label>
                    <input type="text" id="nom" name="nom" readonly value="<?php echo htmlspecialchars($nombre); ?>">
                </div>
                <div class="inputs">
                    <label for="correo">Correo electrónico:</label>
                    <input type="text" id="correo" name="correo" readonly value="<?php echo htmlspecialchars($correo); ?>">
                </div>
                <div class="escolar">
                    <label for="escolaridad">Escolaridad (Último grado de estudios terminado):</label>
                    <select id="escolaridad" name="escolaridad" required>
                        <option value="">-- Seleccione un nivel --</option>
                        <option value="Secundaria">Secundaria</option>
                        <option value="Preparatoria">Preparatoria</option>
                        <option value="Licenciatura">Licenciatura/Ingeniería</option>
                        <option value="Maestría">Maestría</option>
                        <option value="Doctorado">Doctorado</option>
                    </select>  
                </div>              
                <div class="radios" id="soft">
                    <fieldset>
                        <legend>Habilidades blandas:</legend>
                        <div class="soft-columns">
                            <div class="s1">
                                <?php
                                $softs1 = ["Comunicación Efectiva", "Trabajo en Equipo", "Pensamiento Crítico", "Adaptabilidad", "Resolución de Problemas"];
                                foreach ($softs1 as $index => $nombre) {
                                    $id = "H" . ($index + 1);
                                    echo "<div>
                                            <input type='checkbox' id='$id' name='softS[]' value='$nombre'>
                                            <label for='$id'>$nombre</label>
                                          </div>";
                                }
                                ?>
                            </div>
                            <div class="s2">
                                <?php
                                $softs2 = ["Liderazgo", "Inteligencia Emocional", "Gestión de Tiempo", "Empatía", "Creatividad"];
                                foreach ($softs2 as $index => $nombre) {
                                    $id = "H" . ($index + 6);
                                    echo "<div>
                                            <input type='checkbox' id='$id' name='softS[]' value='$nombre'>
                                            <label for='$id'>$nombre</label>
                                          </div>";
                                }
                                ?>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="radios">
                    <fieldset>
                        <legend>Habilidades duras:</legend>
                        <?php
                        $hards = ["HTML", "CSS", "PHP", "SQL", "Java", "C", "Python"];
                        foreach ($hards as $i => $nombre) {
                            $id = "H" . ($i + 11);
                            echo "<div>
                                    <input type='checkbox' id='$id' name='hardS[]' value='$nombre'>
                                    <label for='$id'>$nombre</label>
                                  </div>";
                        }
                        ?>
                    </fieldset>
                </div>

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
                        foreach ($titulos as $i => $nombre) {
                            $id = "H" . ($i + 18);
                            echo "<div>
                                    <input type='radio' id='$id' name='title' value='$nombre' required>
                                    <label for='$id'>$nombre</label>
                                  </div>";
                        }
                        ?>
                    </fieldset>
                </div>

                <input type="submit" name="modificar" value="Modificar">
            </form>
        </div>
    </div>
</body>
</html>

    

    

    <div class="abajo">
        <iframe class="footer" src="../bases/nav-F-User.html"></iframe>
    </div>
</body>
</html>
<?php
session_start();
require_once '../includes/conexion.php';

$correo_usuario = $_SESSION['user'] ?? null;
if (!$correo_usuario) {
    die("No hay sesión activa.");
}

$db = new Database();
$conn = $db->connect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Activar errores PDO

// Obtener ID del reclutador
$sql = "SELECT ID, NOMBRE FROM USUARIO WHERE CORREO = :correo AND TIPO_USUARIO = 2";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':correo', $correo_usuario, PDO::PARAM_STR);
$stmt->execute();
$reclutador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reclutador) {
    die("Reclutador no encontrado.");
}

$id_reclutador = $reclutador['ID'];
$nombre_empresa = $reclutador['NOMBRE'];

// Obtener vacantes
$sqlVacantes = "SELECT * FROM VACANTE WHERE ID_RECLUTADOR = :id";
$stmtVacantes = $conn->prepare($sqlVacantes);
$stmtVacantes->bindValue(':id', $id_reclutador, PDO::PARAM_INT);
$stmtVacantes->execute();
$vacantes = $stmtVacantes->fetchAll(PDO::FETCH_ASSOC);

// FUNCIÓN AJUSTADA A TU BASE DE DATOS
function obtenerTopPostulantes($conn, $id_vacante, $limite = 5) {
    try {
        // Obtener habilidades requeridas
        $sql_habs = "SELECT H.NOMBRE FROM VACANTE_HABILIDAD VH 
                     JOIN HABILIDAD H ON VH.ID_HABILIDAD = H.ID 
                     WHERE VH.ID_VACANTE = :id";
        $stmt = $conn->prepare($sql_habs);
        $stmt->execute([':id' => $id_vacante]);
        $requeridas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Obtener títulos requeridos
        $sql_titulos = "SELECT T.NOMBRE FROM VACANTE_TITULO VT
                        JOIN TITULOS_CV T ON VT.ID_TITULO = T.ID
                        WHERE VT.ID_VACANTE = :id";
        $stmt = $conn->prepare($sql_titulos);
        $stmt->execute([':id' => $id_vacante]);
        $titulos = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $requisitos = array_merge($requeridas, $titulos);
        if (empty($requisitos)) return [];

        $placeholders = implode(',', array_fill(0, count($requisitos), '?'));

        $sql = "
    SELECT U.ID, U.NOMBRE, U.APELLIDOS, U.CORREO, COUNT(*) as coincidencias
    FROM USUARIO U
    JOIN CV ON U.ID = CV.ID_CANDIDATO
    LEFT JOIN CV_HABILIDAD CH ON CH.ID_CV = CV.ID
    LEFT JOIN HABILIDAD H ON H.ID = CH.ID_HABILIDAD
    LEFT JOIN TITULOS_CV TC ON CV.TITULO = TC.NOMBRE
    WHERE U.TIPO_USUARIO = 1 AND (
        H.NOMBRE IN ($placeholders) OR
        TC.NOMBRE IN ($placeholders)
    )
    GROUP BY U.ID
    ORDER BY coincidencias DESC
    LIMIT $limite
";


        $params = array_merge($requisitos, $requisitos);
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener postulantes: " . $e->getMessage());
        return [];
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vacantes Empresa</title>
    <link rel="stylesheet" href="../estilo/VacEmpresas.css" type="text/css">
    <link rel="icon" href="../imagenes/icono.png" type="image/png">
</head>
<body>
<header>
    <iframe class="header" src="../bases/nav-I-Empresa.html"></iframe>
</header>
<main>
    <section class="vacantes-container">
        <h2>Lista de Vacantes</h2>
        <div class="vacantes-lista">
            <?php foreach ($vacantes as $vacante): ?>
            <div class="tarjeta-vacante">
                <h3><?= htmlspecialchars($vacante['NOMBRE']) ?></h3>
                <p class="empresa"><?= htmlspecialchars($nombre_empresa) ?></p>
                <p>$<?= number_format($vacante['SALARIO'], 2) ?> MXN</p>
                <p><?= htmlspecialchars($vacante['MODALIDAD']) ?></p>
                <p><?= htmlspecialchars($vacante['UBICACION']) ?></p>
                <div class="acciones">
                    <a href="editarVacante.php?id=<?php echo $vacante['ID']; ?>" class="btn-editar">Editar</a>
                    <form action="../includes/eliminarVacante.php" method="post" class="form-eliminar">
                        <input type="hidden" name="id_vacante" value="<?php echo $vacante['ID']; ?>">
                        <button type="submit" class="btn-borrar">
                            <img src="../imagenes/trash_5457.png" alt="Borrar" style="width: 30px; height: 30px;">
                        </button>
                    </form>

                </div>
                <div class="postulantes">
                    <h4>Postulantes:</h4>
                    <?php
                    $postulantes = obtenerTopPostulantes($conn, $vacante['ID']);

                    if (empty($postulantes)) {
                        echo '<p class="sin-postulantes">❌ No hay postulantes que cumplan con los requisitos.</p>';
                    } else {
                        foreach ($postulantes as $post) {
                            echo '<div class="postulante">
            
            <div class="datos-postulante">
                <strong>' . htmlspecialchars($post['NOMBRE'] . ' ' . $post['APELLIDOS']) . '</strong><br>
                <small>' . htmlspecialchars($post['CORREO']) . '</small>
            </div>
         
        </div>';
                        }
                    }
                    ?>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<footer>
    <iframe class="footer" src="../bases/nav-F-Empresa.html"></iframe>
</footer>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Evita el envío inmediato

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Envía el formulario si confirma
                }
            });
        });
    });
</script>

</body>
</html>

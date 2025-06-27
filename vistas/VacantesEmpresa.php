<?php
session_start();
require_once '../includes/conexion.php';

$correo_usuario = $_SESSION['user'] ?? null;
if (!$correo_usuario) {
    die("No hay sesión activa.");
}

$db = new Database();
$conn = $db->connect();

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
                    <p>Aún sin implementar</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>
<footer>
    <iframe class="footer" src="../bases/Nav-Final.html"></iframe>
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

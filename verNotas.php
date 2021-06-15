<?php
include 'funciones.php';

csrf();
if (isset($_POST['submit']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    die();
}

$config = include 'config.php';

$resultado = [
    'error' => false,
    'mensaje' => ''
];

if (!isset($_GET['id'])) {
    $resultado['error'] = true;
    $resultado['mensaje'] = 'El alumno no existe';
}

if (isset($_POST['submit'])) {
    try {
        $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
        $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

        $alumno = [
            "id"        => $_GET['id'],
            "nombre"    => $_POST['nombre'],
            "apellido"  => $_POST['apellido'],
            "email"     => $_POST['email'],
            "edad"      => $_POST['edad']
        ];

        $nota = [
            "id"        => $_GET['id'],
            "asignatura"    => $_POST['asignatura'],
            "nota"  => $_POST['nota'],
            "observaciones"     => $_POST['observaciones']
        ];


    } catch(PDOException $error) {
        $resultado['error'] = true;
        $resultado['mensaje'] = $error->getMessage();
    }
}

try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $conexion = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);

    $id = $_GET['id'];
    $consultaSQL = "SELECT * FROM alumnos WHERE id =" . $id;
    $consultaSQLNotas = "SELECT * FROM notas WHERE id_alumno =" . $id;

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute();

    $sentenciaNota = $conexion->prepare($consultaSQLNotas);
    $sentenciaNota->execute();

    $alumno = $sentencia->fetch(PDO::FETCH_ASSOC);
    $notas = $sentenciaNota->fetchAll();

    if (!$alumno) {
        $resultado['error'] = true;
        $resultado['mensaje'] = 'No se ha encontrado el alumno';
    }

} catch(PDOException $error) {
    $resultado['error'] = true;
    $resultado['mensaje'] = $error->getMessage();
}
?>

<?php require "templates/header.php"; ?>

<?php
if ($resultado['error']) {
    ?>
    <div class="container mt-2">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger" role="alert">
                    <?= $resultado['mensaje'] ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mt-4">Notas de <?= escapar($alumno['nombre']) . ' ' . escapar($alumno['apellido'])  ?></h2>
            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Asignatura</th>
                    <th>Nota</th>
                    <th>Observaciones</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($notas && $sentenciaNota->rowCount() > 0) {
                    foreach ($notas as $fila) {
                        ?>
                        <tr>
                            <td><?php echo escapar($fila["id"]); ?></td>
                            <td><?php echo escapar($fila["asignatura"]); ?></td>
                            <td><?php echo escapar($fila["nota"]); ?></td>
                            <td><?php echo escapar($fila["observaciones"]); ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <div class="form-group">
                    <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
                    <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
                </div>
                <tbody>
            </table>
        </div>
    </div>
</div>

<?php require "templates/footer.php"; ?>
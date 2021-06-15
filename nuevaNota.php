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

        $nota = [
        "asignatura"   => $_POST['asignatura'],
        "nota" => $_POST['nota'],
        "observaciones"    => $_POST['observaciones'],
        "id_alumno" => $_GET['id']
        ];

        $consultaSQLNota = "INSERT INTO notas (asignatura, nota, observaciones, id_alumno)";
        $consultaSQLNota .= "values (:" . implode(", :", array_keys($nota)) . ")";

        $sentencia1 = $conexion->prepare($consultaSQLNota);
        $sentencia1->execute($nota);


    } catch(PDOException $error) {
        $resultado['error'] = true;
        $resultado['mensaje'] = $error->getMessage();
    }
}
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

    $id = $_GET['id'];
    $consultaSQL = "SELECT * FROM alumnos WHERE id =" . $id;

    $sentencia = $conexion->prepare($consultaSQL);
    $sentencia->execute();

    $alumno = $sentencia->fetch(PDO::FETCH_ASSOC);

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

<?php
if (isset($_POST['submit']) && !$resultado['error']) {
    ?>
    <div class="container mt-2">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success" role="alert">
                    La nota se ha añadido correctamente
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
            <h2 class="mt-4">Añadir nota para <?= escapar($alumno['nombre']) . ' ' . escapar($alumno['apellido'])  ?></h2>
            <hr>

            </form>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <form method="post">
                <div class="form-group">
                    <label for="asignatura">Asignatura</label>
                    <input type="text" name="asignatura" id="asignatura" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nota">Nota</label>
                    <input type="number" name="nota" id="nota" class="form-control">
                </div>
                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <input type="text" name="observaciones" id="observaciones" class="form-control">
                </div>

                <div class="form-group">
                    <input name="csrf" type="hidden" value="<?php echo escapar($_SESSION['csrf']); ?>">
                    <input type="submit" name="submit" class="btn btn-primary" value="Enviar">
                    <a class="btn btn-primary" href="index.php">Regresar al inicio</a>
                </div>
                <div>

                </div>
            </form>
        </div>
    </div>
</div>


<?php require "templates/footer.php"; ?>
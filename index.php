<?php
$url_completa = $_SERVER['REQUEST_URI'];
$ultimo_segmento = basename($url_completa);
$ultimos_3_digitos = substr($ultimo_segmento, -3);

$carpetaNombre = $ultimos_3_digitos;
$carpetaRuta = "./descarga/" . $carpetaNombre;

try {
    if (!file_exists($carpetaRuta)) {
        mkdir($carpetaRuta, 0755, true);
        $mensaje = "Carpeta '$carpetaNombre' creada con éxito.";
    } else {
        $mensaje = "La carpeta '$carpetaNombre' ya existe.";
    }

    //Modificar la "funcion" para que esta ves puede cargar varios archivos y no solo uno.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['archivos'])) {
            $archivos = $_FILES['archivos'];

            foreach ($archivos['name'] as $key => $name) {
                $tmp_name = $archivos['tmp_name'][$key];
                $error = $archivos['error'][$key];

                //Validacion
                if ($error === 0) {
                //Cambiar los espacios " " por "_"
                    $ruta_destino = $carpetaRuta . '/' . str_replace(' ', '_', $name);
                    if (move_uploaded_file($tmp_name, $ruta_destino)) {
                        $subido = true;
                        $mensaje = "Archivo $name subido con éxito.<br>";
                    } else {
                        $mensaje = "Error al subir el archivo $name.<br>";
                    }
                } else {
                    $mensaje = "Error desconocido al subir el archivo $name.<br>";
                }
            }
        }
    }



    if (isset($_POST['eliminarArchivo'])) {
        $archivoAEliminar = $_POST['eliminarArchivo'];
        $archivoRutaAEliminar = $carpetaRuta . '/' . $archivoAEliminar;

        if (file_exists($archivoRutaAEliminar)) {
            if (unlink($archivoRutaAEliminar)) {
                $mensaje = "Archivo '$archivoAEliminar' eliminado con éxito.";
            } else {
                throw new Exception("Error al eliminar el archivo.");
            }
        } else {
            throw new Exception("El archivo '$archivoAEliminar' no existe.");
        }
    }
} catch (Exception $e) {
    $mensaje = "Error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir archivos</title>
    <script src="parametro.js"></script>
    <link rel="stylesheet" href="estilosss.css">
</head>

<body>
    <h1>COMPARTIR ARCHIVOS <sup class="beta">BETA</sup></h1>

    <div class="content">
        <!-- Solo mostrar los 3 caracteres aleatorios -->
        <h3>Sube y comparte tus archivos al instante de manera segura ahora. <br><span>LINK: localhost<?php echo $url_completa;?></span></h3>
        <div class="container">
            <div class="drop-area" id="drop-area">
                <form action="" id="form" method="POST" enctype="multipart/form-data">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" style="fill:#ff6600;transform: ;msFilter:;"><path d="M13 19v-4h3l-4-5-4 5h3v4z"></path><path d="M7 19h2v-2H7c-1.654 0-3-1.346-3-3 0-1.404 1.199-2.756 2.673-3.015l.581-.102.192-.558C8.149 8.274 9.895 7 12 7c2.757 0 5 2.243 5 5v1h1c1.103 0 2 .897 2 2s-.897 2-2 2h-3v2h3c2.206 0 4-1.794 4-4a4.01 4.01 0 0 0-3.056-3.888C18.507 7.67 15.56 5 12 5 9.244 5 6.85 6.611 5.757 9.15 3.609 9.792 2 11.82 2 14c0 2.757 2.243 5 5 5z"></path></svg> <br>
                    <!-- Modificar el Input para que pueda cargar varios archivos -->
                    <label for="archivo" class="letras">Arrastra tus archivos aqui o <span>Abre el Explorador</span></label>
                    <input type="file" class="file-input" name="archivos[]" id="archivo" onchange="document.getElementById('form').submit()" multiple>
                    <br><br> 
                    
                </form>
            </div>

            <div class="container2">
               

                <div id="file-list" class="pila">
                    <?php
                    $targetDir = $carpetaRuta;

                    $files = scandir($targetDir);
                    $files = array_diff($files, array('.', '..'));

                    if (count($files) > 0) {
                        echo "<h3 style='margin-bottom:10px;'>Archivos Subidos</h3>";

                        foreach ($files as $file) {
                            echo "<div class='archivos_subidos'>
                            <div><a style='display: flex; align-items: center;' href='$carpetaRuta/$file' download class='boton-descargar'><svg style='margin-right: 10px;' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><path d='M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4' /><polyline points='7 10 12 15 17 10' /><line x1='12' y1='15' x2='12' y2='3' /></svg>$file</a></div>
                            <div>
                            <form action='' method='POST' style='display:inline;'>
                                <input type='hidden' name='eliminarArchivo' value='$file'>
                                <button type='submit' class='btn_delete'>
                                    <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-trash' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                        <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                        <path d='M4 7l16 0' />
                                        <path d='M10 11l0 6' />
                                        <path d='M14 11l0 6' />
                                        <path d='M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12' />
                                        <path d='M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3' />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        </div>";
                        }
                    } else {
                        echo "Aun no se han subido archivos";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="parametro.js"></script> -->

</body>

</html>

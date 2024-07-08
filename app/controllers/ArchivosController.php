<?php
require_once './utils/Autentificador.php';
require_once './utils/AutentificadorJWT.php';
require_once './utils/validador.php';

class ArchivosController
{
    public static function descargarCSV($datos)
    {

        $nombreArchivo = "exportacion_" . date("Y-m-d_H-i-s") . ".csv";

        // Configura las cabeceras para la descarga del archivo
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $nombreArchivo);

        // Abre un flujo de escritura para el archivo CSV
        $salida = fopen('php://output', 'w');
        // Escribe la cabecera del CSV (nombres de las columnas)
        $cabecera = array_keys($datos[0]);
        fputcsv($salida, $cabecera);

        // Escribe cada fila en el CSV
        foreach ($datos as $fila) {
            fputcsv($salida, $fila);
        }

        // Cierra el flujo de salida
        fclose($salida);
        exit;
    }
}

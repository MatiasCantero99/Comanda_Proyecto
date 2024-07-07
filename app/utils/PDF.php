<?php
// Incluir FPDF composer require setasign/fpdf
require('./utils/fpdf/fpdf.php');
class PDFGenerator
{
    private $pdf;
    private $logoPath;
    private $title;

    public function __construct()
    {
        // Crear una instancia de FPDF
        $this->pdf = new FPDF();
        $this->logoPath = dirname(__DIR__) . '/utils/fpdf/logo.png';
    }


    public function generatePDFFromAssocArray($data, $fileName = 'datos.pdf', $title = 'Reporte')
    {
            $this->pdf->AddPage();
            $this->pdf->SetFont('Arial', 'B', 8);
    
            // Agregar logo en la cabecera
            $this->addLogo();
        
            $this->addTitle($title);

            if (!empty($data)) {
                if ($this->isAssocArray($data)) {
                    // Caso 1: Array asociativo simple
                    $this->addSimpleAssocArrayToPDF($data);
                } else if (is_array($data) && isset($data[0]) && $this->isAssocArray($data[0])) {
                    // Caso 2: Array de arrays asociativos
                    $this->addAssocArrayOfArraysToPDF($data);
                } else {
                    $this->pdf->Cell(0, 10, 'Formato de datos no soportado', 0, 1);
                }
            } else {
                $this->pdf->Cell(0, 10, 'No hay datos disponibles', 0, 1);
            }

            $this->addTimestamp();
    
            // Salida del PDF
            $this->pdf->Output('D', $fileName); // 'D' fuerza la descarga
        }

    private function addSimpleAssocArrayToPDF($data)
    {
        foreach ($data as $key => $value) {
            $this->pdf->MultiCell(60, 10, $key, 1);
            $this->pdf->MultiCell(150, 10, $value, 1);
            $this->pdf->Ln();
        }
    }

    private function addAssocArrayOfArraysToPDF($data)
    {
        $columns = array_keys($data[0]);
        foreach ($columns as $column) {
            $this->pdf->Cell(40, 10, $column, 1);
        }
        $this->pdf->Ln();
        foreach ($data as $row) {
            foreach ($row as $value) {
                $this->pdf->Cell(40, 10, $value, 1);
            }
            $this->pdf->Ln();
        }
    }

    private function addLogo()
    {
        if (file_exists($this->logoPath)) {
            $this->pdf->Image($this->logoPath, 10, 10, 20);
            $this->pdf->Ln(20);
        }
    }

    private function addTitle($title)
    {
        
            $this->pdf->SetFont('Arial', 'B', 16);
            $this->pdf->Cell(0, 10, $title, 0, 1, 'C');
            $this->pdf->Ln(10);
        
    }

    private function addTimestamp()
    {
        $this->pdf->Ln(10); // Añadir espacio antes de la fecha y hora
        $this->pdf->SetFont('Arial', '', 10);
        $dateTime = date('d-m-Y H:i:s'); 
        $this->pdf->Cell(0, 10, 'Fecha y hora: ' . $dateTime, 0, 1, 'C');
    }

    private function isAssocArray($array)
    {
        if (!is_array($array)) {
            return false;
        }
        return array_keys($array) !== range(0, count($array) - 1);
    }

}


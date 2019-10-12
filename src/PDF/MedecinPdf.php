<?php
/**
 * Created by PhpStorm.
 * User: TNC TECH
 * Date: 12/10/2019
 * Time: 14:33
 */

namespace App\PDF;


class MedecinPdf extends \TCPDF
{

    public $medecins;


    public function LoadData($datas)
    {
        // Read file lines
        $medecins = array();
        foreach ($datas as $data) {
            $medecins[] = explode(';', $data->getNom() . ';' . $data->getPrenom() . ';' . $data->getTelephone() . ';' . $data->getVille());
        }
        return $medecins;
    }

    public function entete($image)
    {
        $this->SetTextColor(255, 193, 7);
        $this->SetFont('helveticaBI', 'B', 30);
        $this->Image($image, 23, 30, 40, 30, 'JPG', '', '', true, 150, '', false, false, 1, false, false, false);
        $this->Cell(150, 40, 'Liste des médecins', 0, 0, 'R', 0);
        $this->Ln(50);
    }

    // Colored table
    public function ColoredTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(1, 133, 119);
        $this->SetTextColor(255, 193, 7);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B', 16);
        // Header
        $w = array(40, 35, 40, 45);
        $num_headers = count($header);
        for ($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        foreach ($data as $row) {
            $this->Cell($w[0], 7, $row[0], 'LR', 0, 'C', $fill);
            $this->Cell($w[1], 7, $row[1], 'LR', 0, 'C', $fill);
            $this->Cell($w[2], 7, number_format($row[2], 0, '.', ' '), 'LR', 0, 'C', $fill);
            $this->Cell($w[3], 7, $row[3], 'LR', 0, 'C', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}
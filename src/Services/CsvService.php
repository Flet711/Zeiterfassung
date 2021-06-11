<?php

namespace App\Services;

class CsvService
{
    public function generateCsvByData(array $header, array $data)
    {
        $file = fopen('php://output', 'wb');
        fputcsv($file, $header);
        foreach ($data as $row) {
            $key = array_keys($row);
            $rowData = [];
            foreach ($key as $iValue) {
                $rowData[] = $row[$iValue];
            }
            fputcsv($file, $rowData);
        }
        fclose($file);
    }
}
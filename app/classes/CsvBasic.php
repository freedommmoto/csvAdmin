<?php

namespace App\classes;

class CsvBasic
{
    public function exportCSV($data, $name = 'data')
    {
        print "\xEF\xBB\xBF"; // UTF-8 BOM
        $csv_output = $this::array2csv($data);

        $append_name = date('Y-m-d H:i:s');
        $filename = $name . '_list_' . $append_name;

        header('Content-Description: File Transfer');
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m-d") . ".csv");
        header("Content-disposition: filename=" . $filename . ".csv");
        header('Content-Transfer-Encoding: binary');
        header('Pragma: public');
        print $csv_output;
        exit;
    }

    public static function array2csv($array)
    {
        if (count($array) == 0) {
            return null;
        }
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, array_keys(reset($array)));
        foreach ($array as $row) {
            fputcsv($df, $row);
        }
        fclose($df);
        return ob_get_clean();
    }
}

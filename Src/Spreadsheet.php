<?php

namespace Src;

use Src\Fs;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * PhpSpreadsheet library wrapper
 * 
 * Manages the data
 * 
 * @author Boudouma Mohamed Ilies <medilies.contact@gmail.com>
 */
class Spreadsheet
{
    private $spreadsheet;

    private $assoc;
    public $fields;

    function __construct(string $spreadsheet_real_path)
    {
        if (Fs::isCsv($spreadsheet_real_path)) {
            $reader = new Csv;
        } elseif (Fs::isOds($spreadsheet_real_path)) {
            $reader = new Ods;
        } elseif (Fs::isXlsx($spreadsheet_real_path)) {
            $reader = new Xlsx;
        } else {
            throw new \Exception("This program only handles CSV, ODS or XLSX spreadsheets and it is unable to the detect the mentionned file extensions");
        }

        $this->spreadsheet = $reader->load($spreadsheet_real_path);
        $this->spreadsheet = $this->spreadsheet->getActiveSheet()->toArray();

        $this->fields = array_shift($this->spreadsheet);
        $this->assoc = array_map(
            fn ($row) => array_combine($this->fields, $row),
            $this->spreadsheet
        );
    }

    public function getAssoc()
    {
        return $this->assoc;
    }
    public function getFields()
    {
        return $this->fields;
    }
}

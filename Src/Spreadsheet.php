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

    private $assoc = [];
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

        foreach ($this->spreadsheet as  $row) {
            if ($this->isRowOfNulls($row)) {
                continue;
            }

            $raw_row = array_map(fn ($item) => trim($item), $row);

            $raw_row = array_combine($this->fields, $raw_row);

            $this->assoc[] = array_filter($raw_row, fn ($v, $k) => !is_null($k) && !empty(trim($k)), ARRAY_FILTER_USE_BOTH);
        }
        $this->fields = array_filter($this->fields, fn ($v) => !is_null($v) && !empty(trim($v)));
    }

    public function getAssoc()
    {
        return $this->assoc;
    }
    public function getFields()
    {
        return $this->fields;
    }

    private function isRowOfNulls(array &$row): bool
    {
        foreach ($row as $cell) {

            if (!is_null($cell) && !empty(trim($cell)))
                return false;
        }
        return true;
    }
}

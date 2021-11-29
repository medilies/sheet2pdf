<?php

namespace Src;

use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

/**
 * @author Boudouma Mohamed Ilies <medilies.contact@gmail.com>
 */
class Spreadsheet
{
    private $spreadsheet;

    private $assoc;
    public $fields;

    function __construct(string $abs_path)
    {
        $reader = new Csv;
        $this->spreadsheet = $reader->load($abs_path);
        $this->spreadsheet = $this->spreadsheet->getActiveSheet()->toArray();

        $this->fields = array_shift($this->spreadsheet);
        $this->assoc = array_map(
            fn ($row) => array_combine($this->fields, $row),
            $this->spreadsheet
        );
        // print_r($this->assoc);
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

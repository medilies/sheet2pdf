<?php

namespace Src;

use \splitbrain\phpcli\PSR3CLI as splitbrainphpcli;
use \splitbrain\phpcli\Options;
use Src\Fs;

/**
 * Methods that controls the program's flow
 * 
 * @author Boudouma Mohamed Ilies <medilies.contact@gmail.com>
 */
class CliUtils extends splitbrainphpcli
{
    /**
     * @var int
     */
    const INPUT_IS_FILE = 0;

    /**
     * @var int
     */
    const INPUT_IS_FOLDER = 1;

    /**
     * @var int
     * equal to self::INPUT_IS_FILE or self::INPUT_IS_FOLDER
     */
    protected $input_type;

    //********************************************************* */
    //              FORCED IMPLEMENTATION
    //********************************************************* */
    protected function setup(Options $options)
    {
    }
    protected function main(Options $options)
    {
    }

    /**
     * Tells whether the user inputed a file or a directory
     * 
     * @param string $real_path
     *
     * @return int self::INPUT_IS_FILE|self::INPUT_IS_FOLDER
     * 
     * @throws \Exception
     * When ambiguis
     */
    protected function inputIsFileOrFolder(string $input_real_path): int
    {
        if (Fs::isDir($input_real_path)) {
            return self::INPUT_IS_FOLDER;
        } else {
            return self::INPUT_IS_FILE;
        }

        throw new \Exception("Cant resolve wether the data-source is a file or a folder");
    }

    /**
     * Sets .pdf file extension and timestamps the name
     * 
     * @param mixed $spreadsheet_file_path
     *
     * @return string
     */
    protected function setPdfNameFromSpreadsheet($spreadsheet_real_path): string
    {
        $file_name = pathinfo($spreadsheet_real_path)['filename'];
        $timestamp = date('o-M-d-H-i-s');
        return "$file_name-$timestamp.pdf";
    }

    /**
     * Uses the output option if set -
     * Else uses the spreadsheet(s) location
     * 
     * @param string|false $output_option
     *
     * @param int $input_type
     *
     * @param string $data_source_real_path
     * MUST be a valid **spreadsheet file** path or a **folder** path
     *
     * @return string
     * absolute folder path
     */
    protected function setOutputFolder(string|false $output_option, int $input_type, string $data_source_real_path)
    {
        // User specified a -o option
        if ($output_option) {

            $output_option = Fs::getRealPathIfExist($output_option);

            if (Fs::isDir($output_option)) {
                return $output_option;
            } else {
                throw new \Exception("<$output_option> specified within the -o option is not a valid folder");
            }
        } else {

            switch ($input_type) {
                case self::INPUT_IS_FILE:
                    return dirname($data_source_real_path);

                case self::INPUT_IS_FOLDER:
                    return $data_source_real_path;
            }
        }
        throw new \Exception("Unexpected behaviour while setting the output location");
    }
}

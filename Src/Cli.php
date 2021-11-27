<?php

namespace Src;

use Exception;
use \splitbrain\phpcli\PSR3CLI as splitbrainphpcli;
use \splitbrain\phpcli\Options;

use Src\Csv2Pdf;

/**
 * @author Boudouma Mohamed Ilies <medilies.contact@gmail.com>
 */
class Cli extends splitbrainphpcli
{

    /**
     * The absolute path of a CSV file or a folder of CSV files
     * 
     * @var string
     */
    protected $data_source;

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

    /**
     * register options and arguments
     */
    protected function setup(Options $options)
    {
        $options->setHelp('Generate PDFs with intresting visuals from a CSV');
        $options->registerOption('version', 'Print version', 'v');

        $options->registerArgument('data-source', 'The CSV file that contains the data rows' . PHP_EOL . 'Or the folder that contains the CSV files', true);
        $options->registerOption('csv', 'The data source is a CSV file', 'c');
        $options->registerOption('folder', '[not supported yet] The data source is a folder of CSV files', 'f'); // no supported yet

        $options->registerArgument('template', 'The HTML template to use' . PHP_EOL . 'The prgram expects a stylesheet (.css) with same name and in the same folder', true);
        // $options->registerOption('style', 'The stylesheet', 's', true);

        $options->registerArgument('records-per-page', 'Max number of data rows to render per page', true);
        $options->registerOption('output', 'Relative or absolute path to where to output the PDF' . PHP_EOL . 'If not specified, the same location of the CSV(s) will be used', 'o', "folder");
    }

    protected function main(Options $options)
    {

        if ($options->getOpt('version')) {
            $this->info('1.0.0');
        } else if ($options->getopt('help')) {
            echo $options->help();
        }

        // Get the arguments
        $args = $options->getArgs();
        // Getting absolute paths OR throw exception
        $this->data_source = $this->getRealPathIfExist($args[0]);
        Csv2Pdf::$template_html_path = $this->getRealPathIfExist($args[1]);
        Csv2Pdf::$template_css_path = $this->getRealPathIfExist(str_replace('html', 'css', Csv2Pdf::$template_html_path));
        // From now on all paths are aboslute and exist
        Csv2Pdf::$max_cards_per_page = $args[2];

        $this->input_type = $this->inputIsFileOrFolder($options);

        Csv2Pdf::$output_folder = $this->setOutputFolder($options->getOpt('output'), $this->input_type, $this->data_source);

        switch ($this->input_type) {
            case self::INPUT_IS_FILE:
                // 
                $csv_file_path = $this->data_source;
                $pdf_file_name = $this->setPdfNameFromCsv($csv_file_path);

                new Csv2Pdf(
                    $csv_file_path,
                    $pdf_file_name,
                );
                break;

            case self::INPUT_IS_FOLDER:
                // 
                // Loop throught folder files
                // 
                break;
        }

        $this->success('Done');
        $this->info('Directed By ROBERT B. WEIDE');
    }

    protected function setPdfNameFromCsv($csv_file_path)
    {
        $csv_name = basename($csv_file_path);
        $pdf_name = str_replace(['.csv', '-csv', '_csv', 'csv'], '', $csv_name);
        $timestamp = date('o-M-d-H-i-s');
        return $pdf_name . '-' . $timestamp . '.pdf';
    }

    /**
     * 
     * Ensure that user picked either --folder or --csv option
     * 
     * @param \splitbrain\phpcli\Options $options
     *
     * @return int self::INPUT_IS_FILE|self::INPUT_IS_FOLDER
     * 
     * @throws \Exception
     * When options are ambiguis
     * 
     */
    protected function inputIsFileOrFolder(Options $options)
    {
        if ($options->getOpt('csv') && $options->getOpt('folder')) {
            throw new \Exception("--file and --csv options cannot be set together. Pick only one!");
        }

        if ($options->getOpt('csv')) {
            return self::INPUT_IS_FILE;
        } elseif ($options->getOpt('folder')) {
            return self::INPUT_IS_FOLDER;
        }

        throw new \Exception("The type of data-source --folder or --csv option must be specified");
    }

    /**
     * Using the output option if set, otherwise output the PDF next to the CSV(s)
     * 
     * @param string|false $output_option
     *
     * @param int $input_type
     *
     * @param string $data_source
     * MUST be a valid **CSV file** path or a **folder** path
     *
     * @return string
     * absolute folder path
     */
    protected function setOutputFolder($output_option, int $input_type, string $data_source)
    {
        // User specified a -o option
        if ($output_option) {
            if ($this->isValidPath($output_option, 'dir')) {
                return realpath($output_option);
            } else {
                throw new \Exception("Invalid folder <$output_option> specified within the -o options");
            }
        }


        switch ($input_type) {
            case self::INPUT_IS_FILE:
                if (!$this->isValidPath($data_source, 'csv')) {
                    throw new \Exception("Invalid CSV <$data_source> specified within the -c options");
                } else {
                    return dirname($data_source);
                }

            case self::INPUT_IS_FOLDER:
                if (!$this->isValidPath($data_source, 'dir')) {
                    throw new \Exception("Invalid folder <$data_source> specified within the -f options");
                } else {
                    return $data_source;
                }
        }

        throw new \Exception("Unexpected behaviour while retrieving output location");
    }

    /**
     * Compares the path against the expected file type (extension)
     * 
     * @param string $real_path
     * A file that surely exist
     *
     * @param 'dir'|'csv'|'html' $file_type
     * The file extension to validate against
     * 
     * @return bool
     * @throws \Exception
     * When validation do not pass
     */
    protected function isValidPath(string $real_path, string $file_type)
    {
        $real_path = $this->getRealPathIfExist($real_path);

        switch ($file_type) {
            case 'dir':
                if (is_dir($real_path)) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'csv':
                if (strcasecmp(pathinfo($real_path)['extension'], 'csv') === 0) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'html':
                if (strcasecmp(pathinfo($real_path)['extension'], 'html') === 0) {
                    return true;
                } else {
                    return false;
                }
                break;
            default:
                throw new \Exception("Unexpected file type: $file_type");
        }

        throw new \Exception("Undexpected behaviour while validatiiing the path $real_path");
    }

    /**
     * Returns an absolute path if the file exist
     * 
     * @param string $path
     * 
     * @return string
     * Absolute path
     * 
     * @throws \Exception
     * When path doesn't exist
     *
     * @uses realpath()
     * @uses file_exists()
     */
    protected function getRealPathIfExist(string $path)
    {
        $real_path = realpath($path);

        if (!$real_path || !file_exists($real_path)) {
            throw new \Exception("$path is invalid");
        }

        return $real_path;
    }
}

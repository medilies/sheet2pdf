<?php

namespace Src;

use \splitbrain\phpcli\Options;
use \Src\Fs;
use \Src\Spreadsheet;
use Src\Pdf;

/**
 * The program's bootstrapper
 * 
 * @author Boudouma Mohamed Ilies <medilies.contact@gmail.com>
 */
class Cli extends CliUtils
{

    /**
     * The absolute path of a spreadsheet  or a folder of spreadsheets
     * 
     * @var string
     */
    protected $data_source;

    /**
     * register options and arguments
     */
    protected function setup(Options $options)
    {
        $options->setHelp('Generate PDFs with intresting visuals from a spreadsheet');
        $options->registerOption('version', 'Print version', 'v');

        $options->registerArgument('data-source', 'The spreadsheet' . PHP_EOL . '[NOT SUPPORTED YET] Or the folder that contains the spreadsheets', true);

        $options->registerArgument('template', 'A .html template to use' . PHP_EOL . 'The prgram expects a stylesheet (.css) with same name and in the same folder', true);
        // $options->registerOption('style', 'The stylesheet', 's', true);

        $options->registerArgument('records-per-page', 'Max number of data rows to render per page', true);

        $options->registerOption('output', 'Relative or absolute path to where to output the PDF' . PHP_EOL . 'If not specified, the same location of the spreadsheet(s) will be used', 'o', "folder");
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

        // Getting absolute paths OR THROW EXCEPTION
        $this->data_source = Fs::getRealPathIfExist($args[0]);
        $this->input_type = $this->inputIsFileOrFolder($this->data_source);

        Pdf::$template_html_path = Fs::getRealPathIfExist($args[1]);
        Pdf::$template_css_path = Fs::getRealPathIfExist(str_replace('html', 'css', Pdf::$template_html_path));
        // From now on all paths are aboslute and exist - except -o if set
        Pdf::$output_folder = $this->setOutputFolder($options->getOpt('output'), $this->input_type, $this->data_source);

        Pdf::$max_cards_per_page = $args[2];

        switch ($this->input_type) {
            case self::INPUT_IS_FILE:
                // 
                $spreadsheet = new Spreadsheet($this->data_source);
                $spreadsheet_real_path = $this->data_source;
                $pdf_file_name = $this->setPdfNameFromSpreadsheet($spreadsheet_real_path);

                new Pdf(
                    $spreadsheet->getAssoc(),
                    $spreadsheet->getFields(),
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
}

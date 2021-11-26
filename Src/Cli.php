<?php

namespace Src;

use \splitbrain\phpcli\PSR3CLI as splitbrainphpcli;
use \splitbrain\phpcli\Options;

use Src\Csv2Pdf;

class Cli extends splitbrainphpcli
{

    protected $data_source;
    protected $html_template;
    protected $template_style;

    // register options and arguments
    protected function setup(Options $options)
    {
        $options->setHelp('Generate PDFs with intresting visuals from a CSV');
        $options->registerOption('version', 'print version', 'v');

        $options->registerArgument('data-source', 'The CSV file that contains data rows' . PHP_EOL . 'Or the folder that contains the CSV files', true);
        $options->registerOption('csv', 'The data source is a CSV file', 'c');
        $options->registerOption('folder', 'The data source is a folder of CSV files', 'f'); // no support yet

        $options->registerArgument('template', 'The HTML template to use within Mpdf' . PHP_EOL . 'The prgramme expects a stylesheet (.css) with same name and in the same folder', true);
        // $options->registerOption('style', 'The stylesheet', 's', true);

        $options->registerArgument('records-per-page', 'Max number of data rows to render per page', true);
        $options->registerOption('output', 'Relative or absolute path to where output the PDF' . PHP_EOL . 'If not specified, the same location of the CSV(s) will be used', 'o', "folder");
    }

    protected function main(Options $options)
    {

        if ($options->getOpt('version')) {
            $this->info('1.0.0');
        } else if ($options->getopt('help')) {
            echo $options->help();
        }

        $args = $options->getArgs();
        $this->data_source = realpath($args[0]);
        $this->html_template = realpath($args[1]);
        $this->template_style = str_replace('html', 'css', $this->html_template);
        Csv2Pdf::$max_cards_per_page = $args[2];

        // Making the PDF(s)
        if ($options->getOpt('csv')) {
            // 
            $csv_file_path = $this->data_source;
            $pdf_file_name = $this->setPdfNameFromCsv($csv_file_path);

            // Using the output option if set and the path is valide, otherwise output the PDF next to the CSV
            $output_folder = $options->getOpt('output') ? realpath($options->getOpt('output')) : dirname($csv_file_path);

            new Csv2Pdf(
                $csv_file_path,
                $pdf_file_name,
                $output_folder,
                $this->html_template,
                $this->template_style
            );
            // 
        } elseif ($options->getOpt('folder')) {
            // 
            // Loop throught folder files
            // 
        } else {
            // $this->error("A --folder or --csv option must be specified according to the data-source");
            throw new \Exception("The type of data-source --folder or --csv option must be specified");
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
}

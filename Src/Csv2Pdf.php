<?php

namespace Src;

use \Mpdf\Mpdf;

class Csv2Pdf
{
    public static $max_cards_per_page;

    protected $csv_file;
    protected $pdf_name;
    protected $output_folder;
    protected $template_html_path;
    protected $template_css_path;

    protected $parsed_csv;
    protected $fields;
    protected $number_of_rows;

    protected $mpdf;


    function __construct($csv_file, $pdf_name, $output_folder, $template_html_path, $template_css_path)
    {
        $this->csv_file = $csv_file;
        $this->pdf_name = $pdf_name;
        $this->output_folder = $output_folder;
        $this->template_html_path = $template_html_path;
        $this->template_css_path = $template_css_path;

        [$this->parsed_csv, $this->fields] = $this->parseCsv($this->csv_file);
        $this->number_of_rows = sizeof($this->parsed_csv);

        $this->mpdf = new Mpdf([
            'orientation' => 'P',
            'margin_top' => 30
        ]);

        $this->mpdf->autoScriptToLang = true;
        $this->mpdf->autoLangToFont = true;
        $this->mpdf->SetDirectionality('ltr');
        $this->mpdf->setFooter('{PAGENO}');

        $this->render();
        $this->setPdfMeta();
        // Dump file
        $output_name = $this->output_folder . "/" . $this->pdf_name;
        $this->mpdf->Output($output_name, 'F');
        echo $output_name . PHP_EOL;
    }

    protected function parseCsv(string $csv_path)
    {
        $parsed_csv = $fields = array();
        $i = 0;

        $handle = @fopen($csv_path, "r");

        if ($handle) {
            while (($row = fgetcsv($handle, 4096)) !== false) {
                if (empty($fields)) {
                    $fields = $row;
                    continue;
                }
                foreach ($row as $k => $value) {
                    $parsed_csv[$i][$fields[$k]] = $value;
                }
                $i++;
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }

        return [$parsed_csv, $fields];
    }

    protected function setPdfMeta()
    {
        $this->mpdf->SetTitle("Cards");
        $this->mpdf->SetAuthor(base64_decode('Qm91ZG91bWEgTW9oYW1lZCBJbGllcw=='));
        $this->mpdf->SetCreator(base64_decode('aHR0cHM6Ly9naXRodWIuY29tL21lZGlsaWVz'));
        $this->mpdf->SetSubject("pdf cards");
        $this->mpdf->SetKeywords("pdf,cards");
    }

    protected function render()
    {
        $template = file_get_contents($this->template_html_path);

        // Write the stylesheet
        $pdf_style = file_get_contents($this->template_css_path);
        $this->mpdf->WriteHTML($pdf_style, 1); // The parameter 1 tells mPDF that this is CSS and not HTML

        $this->mpdf->SetHTMLHeader($this->pdf_name); // redundent

        // Preparing for bulk replace
        $all_possible_patterns =  array_map(
            fn (string $key): string => "%VAR_$key%",
            $this->fields
        );

        for ($i = 0; $i < $this->number_of_rows; $i++) {
            $all_row_data = $this->parsed_csv[$i];

            // Bulk replace: an array of strings(pattenrs) replaced with an array of strings(data)
            $substituted_text = str_replace($all_possible_patterns, $all_row_data, $template);

            // Writting the card
            $this->mpdf->WriteHTML($substituted_text, 2);

            $this->checkForAddingNewPage($i);
        }
    }

    protected function checkForAddingNewPage($iteration): void
    {
        // avoid empty pages
        if ($iteration + 1 === $this->number_of_rows) {
            return;
        }
        if (($iteration + 1) % Csv2Pdf::$max_cards_per_page === 0) {
            $this->mpdf->AddPage();
        }
    }
}

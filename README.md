# CSV2PDF

## Env

1. Enable the `gd` extension in `php.ini`

2. Run:

```text
composer install
```

## Usage example

1. Prepare your CSV file `example_data.csv`

2. Create an HTML template `example.html` which contains only the body tags.

3. Refer to the CSV columns keys in the HTML as `%VAR_<key_name>%`. For example the `name` column from the CSV will be refenced as `%VAR_name%` in the HTML template.

4. Style your template in `example.css`. (Stylesheet name needs to match the HTML file name).

5. Generate the PDF with:

```text
php .\csv2pdf.php -c .\example\example_data.csv .\example\example.html 2
```

> You can name your files (CSV and HTML) and columns names as you like

## NOTE

Knowing the library **MPDF** can help you customize the source code of this project to suit you better

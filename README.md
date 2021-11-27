# CSV2PDF

## Env setup

1. Enable the `gd` extension in `php.ini`

2. Run:

    ```text
    composer install
    ```

    (Can be optional if the vendor file is already present).

## Usage example

In the following example we have a sheet which contains a computers inventory. We need to create and print little cards to stick them in the back of each computer.

1. The CSV inventory sheet is `example_data.csv`:

    | id  | computer_name | owner    | location_department | aquisition_date | cpu    | ram   | disk_size |
    | --- | ------------- | -------- | ------------------- | --------------- | ------ | ----- | --------- |
    | 1   | AIO1          | Mohamed  | Administration      | 01-01-18        | I3     | 4GB   | 200GB     |
    | 2   | AIO2          | Omar     | Administration      | 02-01-18        | I3     | 4GB   | 200GB     |
    | 3   | AIO3          | Othman   | Administration      | 03-01-18        | I3     | 4GB   | 200GB     |
    | 4   | AIO4          | Abubakar | Administration      | 04-01-18        | I3     | 4GB   | 200GB     |
    | 5   | AIO5          | Nadir    | Communication       | 05-01-18        | I3     | 4GB   | 200GB     |
    | 6   | Laptop1       | Sofiane  | Finance             | 05-06-20        | I3     | 4GB   | 200GB     |
    | 7   | Laptop2       | Ilies    | IT                  | 06-06-20        | Ryzen5 | 16GB  | 1TB       |
    | 8   | Laptop3       | Islam    | IT                  | 07-06-20        | Ryzen5 | 16GB  | 1TB       |
    | 9   | SERVER        | none     | IT                  | 01-01-21        | Xeon   | 128GB | 64TB      |

2. Create an HTML template which contains only the body tags. `example.html` will be used for this example.
3. Refer to the CSV columns keys in the HTML as `%VAR_<key_name>%`. For example the `id` column from the CSV will be refenced as `%VAR_id%` in the HTML template:

    ```html
    <p>
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, omnis
        facere a recusandae explicabo quam illum aperiam deleniti autem saepe
        eius possimus officia optio beatae est ratione quibusdam distinctio.
        Reprehenderit?
    </p>
    <h1>%VAR_computer_name%</h1>
    <p>Owner: %VAR_owner%</p>
    <p>Department: %VAR_location_department%</p>
    <table>
        <tr>
            <td>Aquisition Date</td>
            <td>CPU</td>
            <td>RAM</td>
            <td>Storage</td>
        </tr>
        <tr>
            <td>%VAR_aquisition_date%</td>
            <td>%VAR_cpu%</td>
            <td>%VAR_ram%</td>
            <td>%VAR_disk_size%</td>
        </tr>
    </table>
    <hr />
    ```

4. Style your template in `example.css`. (Stylesheet name **MUST** match the HTML file name).

    ```css
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    td {
        padding: 4px 8px;
    }

    hr {
        margin: 16px 0;
    }
    ```

5. Generate the PDF with:

    ```text
    php .\csv2pdf.php -c .\example\example_data.csv .\example\example.html 2
    ```

    - The **-c** option tells the program that we are inputing a single CSV file.
    - A **-o** option is omitted, so the PDF will be outputed next the CSV file, otherwise the PDF will be outputed in the specified location.
    - The first argument **.\example\example_data.csv** is the relative path to the data source.
    - The second argument **.\example\example.html** is the relative path to the template.
    - The third argument **2** tells the program to generate a maximum of two cards per pdf page.

<div align="center">
    <img src="./example/example_data-2021-Nov-27-15-25-55.png" alt="Generated PDF first page" height="600" />
</div>

## NOTE

1. Knowing the library **MPDF** can help you customize the source code of this project to suit you better.
2. You may find that the **MPDF** library is limited when it comes to trasnlating crazy styled HTML and it is the case with other alternative PHP libraries (**Fpdf**, **DOMpdf** ...), for example you cannot output PDFs with _flex_ or _grid_ displays.

## BUGS

One white space is being prepended the first key of the CSV when parsing it!

## TODO

-   Support inputing folders of CSVs with the option **-f**
-   Extend the `%VAR_%` feature. For exmaple:
    -   `%UPPER_str%` will apply `strtoupper()` on `str`.
    -   `%DATE_date:FORMAT%` will allow formating date.
-   Use the **mikehaertl/phpwkhtmltopdf** library instead of **Mpdf**

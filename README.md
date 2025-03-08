# `Data Wizard`

## Usage

The easiest way to use Data Wizard is to use the pre-built Docker container.

```php
docker run \ 
    -p 8000:8000 \
    -e APP_KEY=base64:YOUR_APP_KEY \
    mateffy/data-wizard
```

## Requirements for manual installation

Data Wizard is a Laravel application, so you'll need everything that Laravel requires in order to run.
Most databases should work, but SQLite and Postgres have been tested.

DataWizard uses [`mateffy/llm-magic`](https://github.com/mateffy/llm-magic) for LLM interaction and file data extraction.

In order for file extraction to work you'll need to have [`uv`](https://github.com/astral-sh/uv) installed on your machine and in your PATH.
You can also configure custom paths to use in the `llm-magic.php` config file. For more on this see the [llm-magic documentation](https://github.com/mateffy/llm-magic).

While `llm-magic` uses a custom Python script to extract text and images from PDFs, [`Blaspsoft/doxswap`](https://github.com/Blaspsoft/doxswap) is used for converting Word and other rich text documents to PDF beforehand.
`doxswap` requires that LibreOffice is installed on your machine. You may need to set the `LIBRE_OFFICE_PATH` environment variable to the path of the `soffice` executable.

### Copyright and License

This project is made by [Lukas Mateffy](https://mateffy.me) and is licensed under the [GNU Affero General Public License v3.0 (AGPL-3.0)](https://choosealicense.com/licenses/agpl-3.0/).

For commercial licensing, please drop me an email at [hey@mateffy.me](mailto:hey@mateffy.me).

### Contributing

At the moment, this project is not open for contributions. 
However, if you have ideas, bugs or suggestions, feel free to open an issue or start a discussion!

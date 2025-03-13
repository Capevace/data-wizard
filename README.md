# `Data Wizard`

## Usage

The easiest way to use Data Wizard is to use the pre-built Docker container.

```bash
docker run \
	-p 9090:80 \
	-p 4430:443 \
	-p 4430:443/udp \
	-v storage:/app/storage \
	-v sqlite_data:/app/database \
	-v caddy_data:/data \
	-v caddy_config:/config \
	-e APP_KEY=<REPLACE_WITH_APP_KEY> \
	mateffy/data-wizard:latest
```

You can then access the application at `https://localhost:4430`.

## Requirements for manual installation

Data Wizard is a Laravel application, so you'll need everything that Laravel requires in order to run.
Most databases should work, but SQLite and Postgres have been tested.

DataWizard uses [`mateffy/llm-magic`](https://github.com/mateffy/llm-magic) for LLM interaction and file data extraction.

In order for file extraction to work you'll need to have [`uv`](https://github.com/astral-sh/uv) installed on your machine and in your PATH.
You can also configure custom paths to use in the `llm-magic.php` config file. For more on this see the [llm-magic documentation](https://github.com/mateffy/llm-magic).

While `llm-magic` uses a custom Python script to extract text and images from PDFs, [`Blaspsoft/doxswap`](https://github.com/Blaspsoft/doxswap) is used for converting Word and other rich text documents to PDF beforehand.
`doxswap` requires that LibreOffice is installed on your machine. You may need to set the `LIBRE_OFFICE_PATH` environment variable to the path of the `soffice` executable.

## Thesis

This project was made as part of my 2025 BSc thesis at [Leuphana University Lüneburg](https://leuphana.de). The thesis is available [here](https://github.com/capevace/bachelor-thesis-submission).

### Copyright and License

This project is made by [Lukas Mateffy](https://mateffy.me) and is licensed under the [GNU Affero General Public License v3.0 (AGPL-3.0)](https://choosealicense.com/licenses/agpl-3.0/).

### Contributing

At the moment, this project is not open for contributions.
However, if you have ideas, bugs or suggestions, feel free to open an issue or start a discussion!

# Getting Started

## Pull the Docker image
```bash
docker pull magic-extract/server
```

## Run the server

```bash
docker run -p 8000:8000 magic-extract/server
```

## Run the CLI

```bash
docker run -it --rm -v $PWD:/data magic-extract/cli:latest extract /data/*.pdf
```

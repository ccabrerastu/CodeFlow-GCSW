# CodeFlow

Este repositorio contiene los pasos para construir y ejecutar la imagen de Docker del proyecto **CodeFlow**.

## Requisitos previos

- Tener [Docker](https://www.docker.com/products/docker-desktop) instalado y corriendo en tu sistema operativo.
- Acceso a una terminal o consola (PowerShell, CMD, Bash, etc.).

---

## 1. Clonar el repositorio

Primero, clona este repositorio en tu máquina local:

git clone https://github.com/ccabrerastu/CodeFlow-GCSW

cd codeflow
---
## 2. Construir la imagen (en la carpeta)
docker build -t codeflow .
---
## 3. Ejecutar el contenedor
docker run -d --name codeflow -p 8080:80 -v "${env:USERPROFILE}\Documents\codeflow:/var/www/html" codeflow
---
## 4. Acceder
http://localhost:8080


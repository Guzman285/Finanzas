# 🧱 Base — Estructura MVC PHP

Plantilla base de proyecto PHP con arquitectura MVC propia, namespaces, autoload vía Composer y build de assets con Webpack. Pensada para arrancar nuevos proyectos o migrar proyectos existentes sin reescribir la estructura desde cero.

---

## 🚀 Requisitos

- PHP v7.2.4 o superior
- Extensión `PDO_INFORMIX` (si el proyecto se conecta a Informix)
- Node.js v17.9.0
- npm v8.5
- Composer v2.3 o superior
- Git v2.35 o superior
- `mod_rewrite` activo en el servidor

---

## ⚙️ Pasos para iniciar

El proyecto ya incluye en el repositorio los archivos `.htaccess` (raíz y `public/`) y `.gitignore` necesarios para funcionar — no hace falta crearlos manualmente. Los únicos pasos manuales son crear el `.env` e instalar dependencias.

### 1. Verificar `mod_rewrite`

El servidor debe tener al menos esta configuración:

```apache
<Directory /var/www/html>
    AllowOverride All
</Directory>
```

> En un servidor Ubuntu esta configuración se coloca en `/etc/apache2/sites-available/`

---

### 2. Clonar el repositorio

Clonarlo en la carpeta que se esté utilizando como base en el servidor (ej. `C:\docker`):

```bash
git clone https://github.com/tu-usuario/base.git
```

---

### 3. Crear archivo `.env`

Debe colocarse en `includes/.env` (o la ruta que defina `database.php`), con la información según el entorno donde se ejecute el proyecto:

```env
DEBUG_MODE = 0

DB_HOST=host
DB_SERVICE=port
DB_SERVER=server_name
DB_CONEXION=server_name
DB_USER=usuario
DB_PASS=password
DB_NAME=db_name

APP_NAME = "app_name"
HOST=http://localhost:9002/
```

| Variable | Descripción |
|---|---|
| `DEBUG_MODE` | `1` muestra errores en pantalla, `0` los oculta (usar `0` en producción) |
| `DB_HOST` | Host del servidor de base de datos |
| `DB_SERVICE` | Puerto del servicio Informix |
| `DB_SERVER` | Nombre del servidor definido en `sqlhosts` |
| `DB_CONEXION` | Igual a `DB_SERVER`, usado en el DSN de PDO |
| `DB_USER` / `DB_PASS` | Credenciales de conexión |
| `DB_NAME` | Nombre de la base de datos |
| `APP_NAME` | Nombre del proyecto, usado en vistas/títulos |
| `HOST` | URL base del proyecto |

---

### 4. Instalar paquetes de Node

```bash
npm install
```

---

### 5. Instalar paquetes de Composer

```bash
composer install
```

---

### 6. Construir los archivos de la carpeta pública

```bash
npm run build
```

Para desarrollo, usar en su lugar:

```bash
npm run watch
```

> Este comando permanece en ejecución, vigilando cambios mientras se trabaja en el proyecto.

---

### 7. Configurar versión y descripción del proyecto

Actualizar la información del proyecto y su versión en:

- `package.json`
- `composer.json`

---

## 🗂️ Estructura del proyecto

```
base/
├── classes/            # Clases de soporte / utilitarias
├── controllers/         # Controladores MVC
├── models/              # Modelos (ActiveRecord propio)
├── views/               # Vistas
├── includes/
│   ├── database.php     # Conexión PDO a Informix
│   └── .env              # Variables de entorno (no versionado)
├── public/
│   ├── index.php         # Front controller
│   ├── images/
│   ├── build/             # Assets compilados por Webpack (no versionado)
│   └── .htaccess
├── src/                 # Código fuente JS/SCSS sin compilar
├── Router.php            # Router principal del proyecto
├── composer.json
├── package.json
├── webpack.config.js
├── .htaccess
└── .gitignore
```

---

## 🔐 Variables sensibles

El archivo `.env` y carpetas generadas (`vendor/`, `node_modules/`, `public/build/`) **nunca se suben al repositorio** (están en `.gitignore`). Cada entorno (local, staging, producción) define su propio `.env` manualmente al desplegar.

Los archivos `.htaccess` (raíz y `public/`) sí están versionados en el repositorio, ya que no contienen información sensible y son necesarios para que el proyecto funcione de inmediato al clonar.

---

## 🛠️ Tecnologías

- **Backend:** PHP 7.2+ con namespaces y autoload PSR-4 vía Composer
- **Base de datos:** Informix vía `PDO_INFORMIX`
- **ORM:** ActiveRecord propio
- **Router:** Clase `Router` propia (despacho de controladores/métodos)
- **Frontend:** Bootstrap 5, SweetAlert2, Bootstrap Icons
- **Build:** Webpack 5 + Sass
- **Gestión de dependencias:** Composer (PHP) y npm (JS/CSS)

---

## 📌 Notas de desarrollo

- El proyecto usa una sola entrada (`public/index.php`) como front controller; todas las rutas pasan por el `Router`.
- `public/` es el único directorio expuesto al servidor web — `app`, `includes`, `models`, etc. no son accesibles directamente.
- Los assets (CSS/JS) se compilan a `public/build/` y no se versionan; se regeneran con `npm run build` o `npm run watch`.
- Mantener `composer.json` y `package.json` actualizados con la versión y descripción de cada proyecto derivado de esta base.

---
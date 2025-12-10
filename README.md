# Gestor de Solicitudes - Full Stack (Arquitectura Modular)

Solución Full Stack: API REST (Laravel) + SPA (Vue 3)

## 📋 Stack Tecnológico

### Backend (API Laravel)
- **PHP**: ^8.2 (requiere PHP 8.2 o superior)
- **Laravel**: ^12.0
- **Base de Datos**: SQLite
- **Testing**: PHPUnit ^11.5.3
- **Mocking**: Mockery ^1.6

### Frontend (Vue 3 SPA)
- **Vue.js**: ^3.5.24
- **TypeScript**: ~5.9.3
- **Vite**: ^7.2.4
- **Tailwind CSS**: ^3.4.18
- **PostCSS**: ^8.5.6
- **Autoprefixer**: ^10.4.22
- **Vue TSC**: ^3.1.4

### Herramientas de Desarrollo
- **Node.js**: 18+ (recomendado)
- **Composer**: Última versión estable
- **npm**: Incluido con Node.js
- **Git**: Control de versiones


## 🚀 Pasos para Clonar y Configurar el Proyecto

**Paso 1**: Crear directorio para alojar el proyecto

```bash
mkdir prueba-tecnica
```

**Paso 2**: Crear directorios de la api y la spa

```bash
mkdir api spa
```

**Paso 3**: Ingresar al directorio de la api

```bash
cd api
```

**Paso 4**: Instalar dependencias de PHP

```bash
composer install
```

**Paso 5**: Crear proyecto Laravel

```bash
composer create-project laravel/laravel . --prefer-dist
```

**Paso 6**: Editar .env

```bash
-DB_CONNECTION=sqlite
-DB_DATABASE=database/database.sqlite
```

**Paso 7**: Generar clave de aplicación

```bash
php artisan key:generate
```

**Paso 8**: Volver al directorio raíz del proyecto

```bash
cd ..
```

**Paso 9**: Ingresar al directorio de la spa

```bash
cd spa
```

**Paso 10**: Crear proyecto Vue con Vite sin inicializar el proyecto

```bash
npm create vite@latest . -- --template vue-ts
Install with npm and start now? (N)
```

**Paso 11**: Instalar Tailwind CSS

```bash
npm install -D tailwindcss@^3.4.18 postcss@^8.4.47 autoprefixer@^10.4.22
```

**Paso 12**: Inicializar Tailwind CSS

```bash
npx tailwindcss init -p
```

**Paso 13**: Volver al directorio raíz del proyecto

```bash
cd ..
```

**Paso 14**: Clonar el repositorio de la prueba

```bash
git clone https://github.com/cmoyanoch/prueba-tecnica.git prueba-tecnica-temp
```

**Paso 15**: Copiar contenido (reemplaza "api" y "spa" por los nombres reales de tus carpetas)

```bash
cp prueba-tecnica-temp/*.md prueba-tecnica/
cp prueba-tecnica-temp/.gitignore prueba-tecnica/
cp -r prueba-tecnica-temp/api/* prueba-tecnica/api/
cp -r prueba-tecnica-temp/spa/* prueba-tecnica/spa/
```

**Paso 16**: (Opcional) Inicializar Git en el directorio destino

```bash
cd prueba-tecnica
git init
git remote add origin https://github.com/cmoyanoch/prueba-tecnica.git
git add .
git commit -m "Initial commit: integración con proyecto remoto"
```

**Paso 17**: Volver al directorio raíz del proyecto

```bash
cd ..
```

**Paso 18**: Eliminar carpeta temporal

```bash
rm -rf prueba-tecnica-temp
```

**Paso 19**: Ingresar al directorio de la api

```bash
cd prueba-tecnica/api
```

**Paso 20**: Regenerar autoload

```bash
composer dump-autoload
```

**Paso 21**: Ejecutar migraciones

```bash
php artisan migrate
```

**Paso 22**: Ejecutar seeders para datos de prueba

```bash
php artisan db:seed
```


## 🚀 Ejecución

**Iniciar API**:

```bash
cd api && php artisan serve
```

(Puerto 8000)

**Iniciar SPA**:

```bash
cd spa && npm run dev
```

(Puerto 5173)

**Acceder**: http://localhost:5173

## 📝 Funcionalidades

### Gestión de Solicitudes

#### Operaciones CRUD
- ✅ **Crear solicitud**: Formulario con validación en tiempo real
- ✅ **Listar solicitudes**: Vista paginada con ordenamiento descendente por ID
- ✅ **Actualizar estado**: Cambio de estado (pendiente → aprobado/rechazado, aprobado/rechazado → modificar)
- ✅ **Eliminar solicitud**: Eliminación permanente (hard delete) con confirmación

#### Estados de Solicitud
- 🔵 **Pendiente**: Estado inicial, permite aprobar o rechazar
- ✅ **Aprobado**: Estado final, permite modificar
- ❌ **Rechazado**: Estado final, permite modificar
- 🔄 **Modificar**: Estado intermedio, permite volver a aprobar o rechazar

#### Paginación
- 📄 Paginación configurable (5, 10, 15, 25, 50 elementos por página)
- 🔢 Navegación entre páginas con elipsis inteligente
- 📊 Información de totales (mostrando X a Y de Z resultados)

#### Interfaz de Usuario
- 🎨 Diseño responsive con Tailwind CSS
- 📌 Columnas sticky (Estado, Fecha, Acciones) para mejor UX en tablas grandes
- 🔔 Manejo de errores con mensajes claros
- ⚡ Estados de carga y feedback visual
- 📊 Dashboard con estadísticas (Total de solicitudes, Pendientes)
- ✅ Validación de formularios en tiempo real
- 🗑️ Diálogo de confirmación personalizado para eliminación

### Backend (API Laravel)

#### Arquitectura
- 🏗️ **Arquitectura Modular**: Módulo `Solicitudes` autocontenido
- 🎯 **DDD (Domain-Driven Design)**: Separación en capas (Domain, Application, Infrastructure, HTTP)
- 🔒 **Clean Architecture**: Principios SOLID aplicados
- 🔌 **Repository Pattern**: Abstracción de acceso a datos

#### Validación y Seguridad
- ✅ Validación de requests con Form Requests personalizados
- 🔒 Middleware para asegurar peticiones AJAX
- 📝 Validación de parámetros de paginación
- 🛡️ Validación de estados contra enum

#### Logging y Auditoría
- 📋 Sistema de logging dedicado para auditoría
- 📝 Registro de todas las operaciones (crear, actualizar, eliminar, listar)
- 📊 Logs estructurados en canal `audit`
- 🔍 Trazabilidad completa de cambios

#### Testing
- ✅ **15 tests de integración** (Feature tests)
- ✅ **5 tests unitarios** (Actions y Repository)
- 🧪 Cobertura completa de endpoints API
- 🎯 Tests con mocks para aislar dependencias

### Frontend (Vue 3 SPA)

#### Arquitectura
- 🎨 **Composition API**: Lógica reutilizable con composables
- 📘 **TypeScript**: Tipado estático para mayor seguridad
- 🎯 **Separación de responsabilidades**: Services, Composables, Components, Views

#### Componentes
- 📝 **SolicitudForm**: Formulario de creación con validación
- 🏷️ **EstadoBadge**: Badge visual de estado con colores
- 💬 **ConfirmDialog**: Diálogo de confirmación con Teleport y animaciones
- 📄 **Pagination**: Componente de paginación completo
- 📊 **SolicitudTable**: Tabla con columnas sticky y estados de carga
- 📋 **SolicitudRow**: Fila de tabla con watch automático y manejo de estados

#### Características
- ⚡ Reactividad automática con Vue 3
- 🔄 Manejo de estados con watch y computed properties
- 🎯 Manejo de errores centralizado
- 📡 Comunicación con API mediante servicio dedicado
- 🎨 UI moderna y responsive



## 🧪 Ejecutar Tests del Backend

### Ingresar al directorio de la api

```bash
cd api
```

### Ejecutar todos los tests

```bash
php artisan test
```

### Ejecutar tests del módulo Solicitudes

```bash
php artisan test app/Modules/Solicitudes/Tests/
```

### Ejecutar solo tests Feature

```bash
php artisan test app/Modules/Solicitudes/Tests/Feature/
```

### Ejecutar solo tests Unitarios

```bash
php artisan test app/Modules/Solicitudes/Tests/Unit/
```

### Ejecutar un test específico

```bash
php artisan test app/Modules/Solicitudes/Tests/Feature/SolicitudApiTest.php
```

### Ejecutar con cobertura (si está configurado)

```bash
php artisan test --coverage
```

## 🔍 Verificar Logs de Auditoría API Laravel

### Ingresar al directorio de la api

```bash
cd api
```

### Ver logs en tiempo real

```bash
tail -f storage/logs/audit-$(date +%Y-%m-%d).log
```

### Ver últimas 20 líneas

```bash
tail -n 20 storage/logs/audit-$(date +%Y-%m-%d).log
```

### Buscar operaciones específicas

```bash
grep "solicitud.created" storage/logs/audit*.log
grep "solicitud.estado.updated" storage/logs/audit*.log
grep "solicitudes.listed" storage/logs/audit*.log
```

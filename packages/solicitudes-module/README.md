# Solicitudes Module

Módulo PHP **100% desacoplado** para gestión de solicitudes. Implementa **Clean Architecture** y **Arquitectura Hexagonal**, compatible con Laravel, Symfony y PHP puro.

## 🏗️ Arquitectura

```
solicitudes-module/
├── src/
│   ├── Domain/                    # ← PHP PURO (Corazón del negocio)
│   │   ├── Entities/              # Entidades de dominio
│   │   ├── ValueObjects/          # Value Objects inmutables
│   │   ├── Enums/                 # Enumeraciones
│   │   ├── Events/                # Eventos de dominio
│   │   ├── Exceptions/            # Excepciones de dominio
│   │   └── Contracts/             # Interfaces (Ports)
│   │
│   ├── Application/               # ← PHP PURO (Casos de Uso)
│   │   ├── UseCases/              # Casos de uso
│   │   └── DTOs/                  # Data Transfer Objects
│   │
│   └── Infrastructure/            # ← Adapters (Implementaciones)
│       ├── Persistence/
│       │   ├── Eloquent/          # Para Laravel
│       │   ├── Doctrine/          # Para Symfony
│       │   └── PDO/               # PHP Puro
│       └── Services/
│
└── adapters/                      # ← Integraciones específicas
    ├── laravel/                   # Service Provider, Controller, etc.
    └── symfony/                   # Bundle, Controller, etc.
```

## ✨ Características

- **100% Desacoplado**: El dominio y la aplicación no dependen de ningún framework
- **SOLID**: Sigue estrictamente los principios SOLID
- **Clean Architecture**: Separación clara de capas
- **Múltiples ORMs**: Soporta Eloquent (Laravel), Doctrine (Symfony) y PDO puro
- **Eventos de Dominio**: Sistema de eventos para comunicación desacoplada
- **Value Objects**: Validación automática en la creación
- **DTOs**: Transferencia de datos tipada
- **API Ready**: Controladores listos para Vue.js/React/Angular

## 📦 Instalación

```bash
composer require myvendor/solicitudes-module
```

### Laravel

```php
// config/app.php
'providers' => [
    // ...
    SolicitudesModule\Adapters\Laravel\SolicitudesModuleServiceProvider::class,
],
```

Publicar configuración y migraciones:

```bash
php artisan vendor:publish --tag=solicitudes-config
php artisan vendor:publish --tag=solicitudes-migrations
php artisan migrate
```

### Symfony

```php
// config/bundles.php
return [
    // ...
    SolicitudesModule\Adapters\Symfony\SolicitudesBundle::class => ['all' => true],
];
```

### PHP Puro

```php
use SolicitudesModule\Application\UseCases\CreateSolicitudUseCase;
use SolicitudesModule\Infrastructure\Persistence\PDO\PDOSolicitudRepository;
use SolicitudesModule\Infrastructure\Services\FileAuditLogger;

$pdo = new PDO('mysql:host=localhost;dbname=myapp', 'user', 'pass');
$repository = new PDOSolicitudRepository($pdo);
$auditLogger = new FileAuditLogger('/var/log/solicitudes.log');

$useCase = new CreateSolicitudUseCase($repository, $auditLogger);
```

## 🚀 Uso

### Crear una Solicitud

```php
use SolicitudesModule\Application\DTOs\CreateSolicitudDTO;
use SolicitudesModule\Application\UseCases\CreateSolicitudUseCase;

$dto = new CreateSolicitudDTO(nombreDocumento: 'Mi Documento');
$solicitudDTO = $createUseCase->execute($dto);

echo $solicitudDTO->id; // 1
echo $solicitudDTO->estado; // "pendiente"
```

### Listar Solicitudes

```php
use SolicitudesModule\Application\DTOs\ListSolicitudesDTO;
use SolicitudesModule\Application\UseCases\ListSolicitudesUseCase;

$dto = new ListSolicitudesDTO(page: 1, perPage: 10);
$result = $listUseCase->execute($dto);

foreach ($result->items() as $solicitud) {
    echo $solicitud->nombreDocumento;
}

echo $result->total(); // Total de registros
echo $result->lastPage(); // Última página
```

### Actualizar Estado

```php
use SolicitudesModule\Application\DTOs\UpdateEstadoDTO;
use SolicitudesModule\Domain\Enums\EstadoSolicitud;

$dto = new UpdateEstadoDTO(
    solicitudId: 1,
    estado: EstadoSolicitud::APROBADO
);

$solicitudDTO = $updateEstadoUseCase->execute($dto);
```

### Eliminar Solicitud

```php
$deleted = $deleteUseCase->execute(solicitudId: 1);
```

## 🔄 Cambiar de ORM/Base de Datos

El módulo permite cambiar fácilmente entre diferentes implementaciones de persistencia:

### Usar PDO en lugar de Eloquent

```php
// En Laravel, sobrescribe el binding
$this->app->bind(
    SolicitudRepositoryInterface::class,
    function ($app) {
        return new PDOSolicitudRepository(
            new PDO('sqlite:' . database_path('database.sqlite'))
        );
    }
);
```

### Usar Doctrine

```php
$repository = new DoctrineSolicitudRepository($entityManager);
```

## 📡 API Endpoints

El módulo expone los siguientes endpoints REST:

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/solicitudes` | Listar (paginado) |
| GET | `/api/solicitudes/{id}` | Obtener una |
| POST | `/api/solicitudes` | Crear |
| PUT/PATCH | `/api/solicitudes/{id}` | Actualizar estado |
| DELETE | `/api/solicitudes/{id}` | Eliminar |

### Ejemplo de respuesta

```json
{
    "data": {
        "id": 1,
        "nombre_documento": "Mi Documento",
        "estado": "pendiente",
        "estado_label": "Pendiente",
        "estado_color": "warning",
        "created_at": "2024-01-01 10:00:00",
        "updated_at": "2024-01-01 10:00:00",
        "puede_ser_aprobada": true,
        "puede_ser_rechazada": true,
        "puede_ser_modificada": false,
        "puede_ser_eliminada": true
    }
}
```

## 🧪 Testing

```bash
composer test
```

## 📄 Licencia

MIT

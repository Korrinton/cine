# Tu Cine del Barrio

Aplicación web de gestión y reserva de entradas de cine desarrollada como proyecto académico.

## Descripción

Plataforma que permite a los usuarios consultar la cartelera, seleccionar asientos y comprar entradas online. Incluye un panel de administración para gestionar películas, eventos, salas y ver la recaudación.

## Funcionalidades

### Usuarios
- Registro e inicio de sesión
- Consulta de cartelera con películas en proyección
- Selección de fecha y horario de sesión
- Elección de asientos en un mapa interactivo
- Historial de compras

### Administración
- Gestión de películas (crear, eliminar)
- Gestión de salas (crear, eliminar)
- Gestión de eventos (crear, editar, eliminar) con control de solapamiento de salas
- Horarios de sesión automáticos según duración de la película
- Precios dinámicos: descuento matinal, miércoles y recargo fin de semana
- Panel de recaudación con gastos e ingresos extra
- Pagos online mediante Stripe Checkout

## Tecnologías

- **Backend:** PHP 8 · Laravel 11
- **Frontend:** Blade · Bootstrap 5 · JavaScript
- **Base de datos:** MySQL
- **Pagos:** Stripe Checkout
- **Servidor:** Nginx · Docker
- **Despliegue:** VPS con Docker Compose

## Instalación local

### Requisitos
- Docker y Docker Compose

### Pasos

```bash
gh repo clone Korrinton/cine
cd cine
cp .env.example .env
```

Edita `.env` con tus credenciales de base de datos y las claves de Stripe, y luego:

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

La aplicación estará disponible en `http://localhost`.

> **Stripe en modo test:** usa la tarjeta `4242 4242 4242 4242`, cualquier fecha futura y cualquier CVC.

## Estructura del proyecto

```
cine/
├── app/
│   ├── Http/Controllers/   # Controladores (Admin, Reserva, Usuario...)
│   └── Models/             # Modelos Eloquent
├── resources/views/        # Vistas Blade
│   ├── admin/              # Panel de administración
│   └── reservas/           # Flujo de reserva
├── routes/web.php          # Rutas
├── database/migrations/    # Migraciones
└── docker-compose.yml      # Configuración Docker
```

## Autor

Proyecto académico desarrollado por Ramón Berzosa Pedroche, Miguel Fernández Guerrero,Ángel Gil Moreno y Enrique Pedregal Garrido.

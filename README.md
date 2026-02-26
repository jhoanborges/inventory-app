# Sistema de Inventario de Bodega

Sistema completo de inventario para bodega con 3 aplicaciones:

- **Backend**: Laravel Sail + Filament + PostgreSQL + Spatie Permission + Sanctum
- **Desktop**: Tauri v2 + Next.js + React + TypeScript + shadcn/ui + Tailwind + Redux + Axios + ExcelJS
- **Mobile**: React Native (bare, sin Expo) + Paper + Maps + Camera + Redux + Axios

```
inventario-bodega/
├── backend/              ← Laravel Sail
├── desktop/              ← Tauri + Next.js
├── mobile/               ← React Native
└── README.md
```

---

## Requisitos previos

- **Docker Desktop** (para Laravel Sail)
- **PHP 8.2+** y **Composer**
- **Node.js 18+** y **yarn**
- **Rust** (para Tauri) → https://www.rust-lang.org/tools/install
- **Android Studio** (para React Native Android)
- **Xcode** (solo macOS, para React Native iOS)

---

## PASO 1: Backend (Laravel Sail)

### 1.1 Crear proyecto

```bash
cd inventario-bodega
curl -s "https://laravel.build/backend?with=pgsql,redis" | bash
cd backend
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
```

### 1.2 Instalar paquetes

```bash
./vendor/bin/sail composer require filament/filament:"^3.2"
./vendor/bin/sail composer require spatie/laravel-permission:"^6.4"
# Sanctum ya viene incluido en Laravel 11+
```

### 1.3 Publicar configuraciones

```bash
./vendor/bin/sail artisan filament:install --panels
./vendor/bin/sail artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### 1.4 Crear Enums

```bash
mkdir -p app/Enums
```

Crear manualmente estos 3 archivos:

**app/Enums/EstadoLote.php**
```php
<?php

namespace App\Enums;

enum EstadoLote: string
{
    case Activo = 'activo';
    case Vencido = 'vencido';
    case Agotado = 'agotado';
}
```

**app/Enums/EstadoRuta.php**
```php
<?php

namespace App\Enums;

enum EstadoRuta: string
{
    case Pendiente = 'pendiente';
    case EnProgreso = 'en_progreso';
    case Completada = 'completada';
}
```

**app/Enums/TipoMovimiento.php**
```php
<?php

namespace App\Enums;

enum TipoMovimiento: string
{
    case Entrada = 'entrada';
    case Salida = 'salida';
}
```

### 1.5 Crear Modelos y Migraciones

```bash
./vendor/bin/sail artisan make:model Producto -m
./vendor/bin/sail artisan make:model Lote -m
./vendor/bin/sail artisan make:model Ruta -m
./vendor/bin/sail artisan make:model MovimientoInventario -m
```

#### Migración: productos
```php
Schema::create('productos', function (Blueprint $table) {
    $table->id();
    $table->string('sku')->unique();
    $table->string('nombre');
    $table->text('descripcion')->nullable();
    $table->string('categoria')->nullable();
    $table->string('unidad_medida'); // kg, unidad, caja
    $table->decimal('precio', 10, 2)->nullable();
    $table->integer('stock_actual')->default(0);
    $table->integer('stock_minimo')->default(0);
    $table->string('barcode')->unique()->nullable();
    $table->string('imagen')->nullable();
    $table->boolean('activo')->default(true);
    $table->timestamps();
});
```

#### Migración: lotes
```php
Schema::create('lotes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
    $table->string('numero_lote')->unique();
    $table->integer('cantidad');
    $table->date('fecha_fabricacion')->nullable();
    $table->date('fecha_vencimiento')->nullable();
    $table->string('estado')->default('activo'); // activo, vencido, agotado
    $table->timestamps();
});
```

#### Migración: rutas
```php
Schema::create('rutas', function (Blueprint $table) {
    $table->id();
    $table->string('nombre');
    $table->string('origen');
    $table->string('destino');
    $table->foreignId('operador_id')->nullable()->constrained('users')->nullOnDelete();
    $table->string('vehiculo')->nullable();
    $table->string('estado')->default('pendiente'); // pendiente, en_progreso, completada
    $table->dateTime('fecha_inicio')->nullable();
    $table->dateTime('fecha_fin')->nullable();
    $table->timestamps();
});
```

#### Migración: movimientos_inventario
```php
Schema::create('movimiento_inventarios', function (Blueprint $table) {
    $table->id();
    $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
    $table->foreignId('lote_id')->nullable()->constrained('lotes')->nullOnDelete();
    $table->foreignId('ruta_id')->nullable()->constrained('rutas')->nullOnDelete();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->string('tipo'); // entrada, salida
    $table->integer('cantidad');
    $table->string('motivo')->nullable();
    $table->timestamps();
});
```

### 1.6 Configurar Modelos

**Producto.php** - fillable: sku, nombre, descripcion, categoria, unidad_medida, precio, stock_actual, stock_minimo, barcode, imagen, activo. Relaciones: `hasMany(Lote)`, `hasMany(MovimientoInventario)`.

**Lote.php** - fillable: producto_id, numero_lote, cantidad, fecha_fabricacion, fecha_vencimiento, estado. Cast estado a `EstadoLote`. Relación: `belongsTo(Producto)`.

**Ruta.php** - fillable: nombre, origen, destino, operador_id, vehiculo, estado, fecha_inicio, fecha_fin. Cast estado a `EstadoRuta`. Relación: `belongsTo(User, 'operador_id')`.

**MovimientoInventario.php** - fillable: producto_id, lote_id, ruta_id, user_id, tipo, cantidad, motivo. Cast tipo a `TipoMovimiento`. Relaciones: `belongsTo(Producto)`, `belongsTo(Lote)`, `belongsTo(Ruta)`, `belongsTo(User)`.

**User.php** - Agregar traits: `HasApiTokens`, `HasRoles`. Implementar `FilamentUser`. Relaciones: `hasMany(Ruta, 'operador_id')`, `hasMany(MovimientoInventario)`.

### 1.7 Seeders

```bash
./vendor/bin/sail artisan make:seeder RoleSeeder
./vendor/bin/sail artisan make:seeder AdminUserSeeder
```

**RoleSeeder.php**: Crear roles `admin`, `supervisor`, `operador`.

**AdminUserSeeder.php**: Crear usuario admin@bodega.com / password, asignar rol admin.

**DatabaseSeeder.php**: Llamar a RoleSeeder y AdminUserSeeder.

```bash
./vendor/bin/sail artisan migrate --seed
```

### 1.8 Filament Resources

```bash
./vendor/bin/sail artisan make:filament-resource Producto --generate
./vendor/bin/sail artisan make:filament-resource Lote --generate
./vendor/bin/sail artisan make:filament-resource Ruta --generate
./vendor/bin/sail artisan make:filament-resource MovimientoInventario --generate
./vendor/bin/sail artisan make:filament-resource User --generate
```

Personalizar cada Resource con los campos correspondientes del modelo.

### 1.9 API Controllers

```bash
./vendor/bin/sail artisan make:controller Api/AuthController
./vendor/bin/sail artisan make:controller Api/ProductoController --api
./vendor/bin/sail artisan make:controller Api/LoteController --api
./vendor/bin/sail artisan make:controller Api/RutaController --api
./vendor/bin/sail artisan make:controller Api/MovimientoController --api
./vendor/bin/sail artisan make:controller Api/ScanController
```

### 1.10 API Resources

```bash
./vendor/bin/sail artisan make:resource ProductoResource
./vendor/bin/sail artisan make:resource LoteResource
./vendor/bin/sail artisan make:resource RutaResource
./vendor/bin/sail artisan make:resource MovimientoResource
```

### 1.11 Rutas API (routes/api.php)

```php
use App\Http\Controllers\Api\{AuthController, ProductoController, LoteController, RutaController, MovimientoController, ScanController};

Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    Route::apiResource('productos', ProductoController::class);
    Route::post('/productos/sync', [ProductoController::class, 'sync']);

    Route::apiResource('lotes', LoteController::class);
    Route::apiResource('rutas', RutaController::class);

    Route::get('/movimientos', [MovimientoController::class, 'index']);
    Route::post('/movimientos', [MovimientoController::class, 'store']);

    Route::get('/scan/{barcode}', [ScanController::class, 'scan']);
});
```

### 1.12 CORS (config/cors.php)

Agregar los orígenes del desktop y mobile:
```php
'allowed_origins' => [
    'http://localhost:3000',
    'http://localhost:1420',
    'tauri://localhost',
],
```

---

## PASO 2: Desktop (Tauri v2 + Next.js)

### 2.1 Crear proyecto Next.js

```bash
cd inventario-bodega
yarn create next-app desktop --typescript --tailwind --eslint --app --src-dir
cd desktop
```

### 2.2 Configurar Next.js para Tauri

Editar `next.config.js`:
```js
/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'export',
  images: { unoptimized: true },
};
module.exports = nextConfig;
```

### 2.3 Instalar Tauri v2

```bash
yarn add -D @tauri-apps/cli@next
yarn tauri init
```

Durante `tauri init`:
- App name: `Inventario Bodega`
- Window title: `Inventario Bodega`
- Web assets path: `../out`
- Dev server URL: `http://localhost:3000`
- Dev command: `yarn dev`
- Build command: `yarn build`

### 2.4 Instalar dependencias

```bash
yarn add @tauri-apps/api@next
yarn add @reduxjs/toolkit react-redux
yarn add axios
yarn add exceljs file-saver
yarn add lucide-react
yarn add @types/file-saver -D
```

### 2.5 Inicializar shadcn/ui

```bash
npx shadcn@latest init
npx shadcn@latest add button card input label table select dialog sheet badge separator dropdown-menu toast tabs
```

### 2.6 Estructura de carpetas a crear

```
desktop/src/
├── app/
│   ├── layout.tsx               ← layout con sidebar
│   ├── page.tsx                 ← Dashboard
│   ├── productos/page.tsx
│   ├── lotes/page.tsx
│   ├── rutas/page.tsx
│   ├── movimientos/page.tsx
│   └── importar/page.tsx        ← Excel import/export
├── components/
│   ├── ui/                      ← (generado por shadcn)
│   ├── layout/
│   │   ├── Sidebar.tsx
│   │   └── Header.tsx
│   ├── productos/
│   │   └── ProductosTable.tsx
│   └── excel/
│       ├── ExcelImport.tsx
│       └── ExcelExport.tsx
├── store/
│   ├── store.ts
│   ├── hooks.ts
│   ├── productosSlice.ts
│   ├── lotesSlice.ts
│   ├── rutasSlice.ts
│   └── movimientosSlice.ts
├── services/
│   └── api.ts                   ← Axios instance
└── types/
    └── index.ts                 ← TypeScript interfaces
```

### 2.7 Ejecutar en desarrollo

```bash
yarn tauri dev
```

---

## PASO 3: Mobile (React Native bare)

### 3.1 Crear proyecto

```bash
cd inventario-bodega
npx @react-native-community/cli init mobile --template react-native-template-typescript
cd mobile
```

### 3.2 Instalar dependencias

```bash
# Navegación
yarn add @react-navigation/native @react-navigation/stack @react-navigation/bottom-tabs
yarn add react-native-screens react-native-safe-area-context react-native-gesture-handler

# UI
yarn add react-native-paper react-native-vector-icons

# Estado y HTTP
yarn add @reduxjs/toolkit react-redux axios

# Funcionalidades nativas
yarn add react-native-maps
yarn add react-native-camera
yarn add @react-native-async-storage/async-storage

# iOS pods (solo macOS)
cd ios && pod install && cd ..
```

### 3.3 Estructura de carpetas a crear

```
mobile/src/
├── screens/
│   ├── LoginScreen.tsx
│   ├── HomeScreen.tsx
│   ├── ScannerScreen.tsx
│   ├── ProductDetailScreen.tsx
│   ├── MovimientoScreen.tsx
│   ├── RutasScreen.tsx
│   ├── RutaDetailScreen.tsx
│   └── MapScreen.tsx
├── components/
│   ├── ProductCard.tsx
│   ├── MovimientoForm.tsx
│   └── MapMarker.tsx
├── navigation/
│   └── AppNavigator.tsx
├── store/
│   ├── store.ts
│   ├── hooks.ts
│   ├── productosSlice.ts
│   ├── movimientosSlice.ts
│   └── rutasSlice.ts
├── services/
│   └── api.ts
├── types/
│   └── index.ts
└── utils/
    └── index.ts
```

### 3.4 Configurar API base URL

En `mobile/src/services/api.ts`, usar la IP de tu máquina (no `localhost`) para que el emulador/dispositivo pueda conectarse:

```typescript
const api = axios.create({
  baseURL: 'http://10.0.2.2:80/api', // Android emulator → host machine
  // baseURL: 'http://192.168.x.x:80/api', // Dispositivo físico
});
```

### 3.5 Ejecutar

```bash
# Android
yarn android

# iOS (solo macOS)
yarn ios
```

---

## Endpoints API - Referencia rápida

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | /api/auth/login | Login, devuelve token |
| POST | /api/auth/logout | Logout, revoca token |
| GET | /api/auth/user | Usuario autenticado |
| GET | /api/productos | Listar productos |
| GET | /api/productos/{id} | Detalle producto |
| POST | /api/productos | Crear producto |
| PUT | /api/productos/{id} | Actualizar producto |
| DELETE | /api/productos/{id} | Eliminar producto |
| POST | /api/productos/sync | Bulk import desde Excel |
| GET | /api/lotes | Listar lotes |
| GET | /api/lotes/{id} | Detalle lote |
| POST | /api/lotes | Crear lote |
| PUT | /api/lotes/{id} | Actualizar lote |
| GET | /api/rutas | Listar rutas |
| GET | /api/rutas/{id} | Detalle ruta |
| POST | /api/rutas | Crear ruta |
| PUT | /api/rutas/{id} | Actualizar ruta |
| GET | /api/movimientos | Listar movimientos |
| POST | /api/movimientos | Registrar movimiento |
| GET | /api/scan/{barcode} | Buscar producto por barcode |

---

## Credenciales por defecto

| Campo | Valor |
|-------|-------|
| Email | admin@bodega.com |
| Password | password |
| Panel admin | http://localhost/admin |

---

## Roles

| Rol | Acceso |
|-----|--------|
| admin | Panel Filament + API completa |
| supervisor | Panel Filament + API completa |
| operador | Solo API (mobile) |

# Comandos para ejecutar el proyecto

## Backend (Laravel Sail)

```bash
cd backend

# Levantar los contenedores
sail up -d

# Ejecutar migraciones y seeders
sail artisan migrate --seed

# Crear usuario admin de Filament
sail artisan make:filament-user

# Detener los contenedores
sail down

# Reconstruir contenedores (si se modifica el Dockerfile)
sail build --no-cache
sail up -d

# Limpiar caché
sail artisan config:clear
sail artisan cache:clear
sail artisan route:clear

# Accesos
# Panel admin: http://localhost/admin
# API:         http://localhost/api
# Credenciales: admin@bodega.com / password
```

## Desktop (Tauri + Next.js)

```bash
cd desktop

# Instalar dependencias
yarn install

# Desarrollo (Next.js en navegador)
yarn dev

# Desarrollo (app Tauri)
yarn tauri dev

# Build de producción
yarn build
yarn tauri build
```

## Mobile (React Native)

```bash
cd mobile

# Instalar dependencias
yarn install

# Android
yarn android

# iOS (solo macOS)
cd ios && pod install && cd ..
yarn ios

# Metro bundler (si no arranca automáticamente)
yarn start
```
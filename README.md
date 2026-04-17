# Ciclismo — Gestión de Resultados Ciclistas

Sistema avanzado para la gestión de pruebas ciclistas, etapas y clasificaciones, construido con tecnologías de vanguardia en el ecosistema Laravel.

## 🚀 Tecnologías Principales

- **Laravel 13**: El framework PHP del siglo XXI.
- **Livewire 4**: Para interfaces dinámicas y reactivas sin salir de PHP.
- **Flux UI**: Componentes premium para una experiencia de usuario excepcional.
- **Laravel Fortify**: Sistema de autenticación robusto y personalizable.
- **Tailwind CSS**: Estilizado moderno y responsivo.

## ✨ Características Principales

### Para Administradores (Manager)
- **Gestión de Pruebas**: Creación y edición de carreras ciclistas (Clásicas o por etapas).
- **Control de Etapas**: Configuración detallada de cada jornada (tipo de etapa, kilometraje, salida/llegada).
- **Entrada de Tiempos**: Sistema optimizado para registrar los resultados de los corredores en cada etapa.
- **Bonificaciones y Penalizaciones**: Gestión precisa de segundos extra o sanciones.
- **Base de Datos de Ciclistas y Equipos**: Mantenimiento completo de participantes y escuadras.

### Área Pública
- **Clasificaciones en Tiempo Real**:
    - **General**: Clasificación por tiempos acumulados.
    - **Puntos**: Seguimiento de la regularidad.
    - **Equipos**: Suma de los 3 mejores tiempos de cada escuadra.
- **Fichas de Ciclista**: Historial detallado de participaciones y resultados por corredor.
- **Detalle de Etapa**: Resultados individuales de cada jornada.

### Seguridad
- **Autenticación con Fortify**: Registro, login y gestión de perfil.
- **Doble Factor de Autenticación (2FA)**: Seguridad extra para las cuentas de administrador.
- **Gestión de Sesiones y Navegadores**: Control total sobre los accesos a la cuenta.

## 🛠️ Instalación y Configuración

1. **Clonar el repositorio**:
   ```bash
   git clone https://github.com/tu-usuario/ciclismo.git
   cd ciclismo
   ```

2. **Instalar dependencias de PHP**:
   ```bash
   composer install
   ```

3. **Instalar dependencias de Frontend**:
   ```bash
   npm install
   ```

4. **Configurar el entorno**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configurar la base de datos**:
   Edita el archivo `.env` con tus credenciales y ejecuta las migraciones:
   ```bash
   php artisan migrate
   ```

6. **Compilar assets y arrancar**:
   ```bash
   npm run dev
   ```
   *O usa el comando de ayuda definido en composer:*
   ```bash
   composer run dev
   ```

---

Desarrollado con ❤️ para los amantes del ciclismo.

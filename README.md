# 🐎 Caballos para disfrutar - Backend Laravel

API REST desarrollada en Laravel para gestionar las reservas online del centro ecuestre **Caballos para disfrutar**.

El sistema permite gestionar usuarios, caballos, reservas, pagos, emails de confirmación y notificaciones por WhatsApp.

---

## 📌 Objetivo del proyecto

El centro ecuestre necesita una plataforma para facilitar la reserva online de paseos de hípica durante los fines de semana.

El backend ofrece una API REST para ser consumida tanto desde una aplicación web como desde una aplicación Android desarrollada en Kotlin.

---

## 🛠️ Tecnologías utilizadas

- Laravel
- PHP
- MySQL
- Laravel Sanctum
- API REST
- Mail SMTP
- CallMeBot WhatsApp API
- Composer
- XAMPP / entorno LAMP
- JSON
- MVC

---

## 🧱 Arquitectura

El backend sigue la arquitectura MVC de Laravel:

```txt
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       ├── ReservaController.php
│   │       ├── CaballoController.php
│   │       ├── PagoController.php
│   │       └── AdminController.php
│   └── Middleware/
├── Models/
│   ├── User.php
│   ├── Reserva.php
│   ├── Caballo.php
│   └── Pago.php
├── Mail/
│   └── ReservaCreadaMail.php
└── routes/
    └── api.php
🔐 Autenticación

La autenticación se realiza mediante Laravel Sanctum.

El usuario inicia sesión y recibe un token Bearer.

Ejemplo de cabecera:

Authorization: Bearer TOKEN_DEL_USUARIO
Accept: application/json
👤 Usuarios

El sistema permite:

Registro de usuarios
Login
Logout
Consulta de usuario autenticado
Diferenciación entre usuario normal y administrador

Campos principales:

id
nombre
email
telefono
password
rol
🐴 Gestión de caballos

Cada caballo contiene:

Nombre
Raza
Fecha de nacimiento
Foto
Estado de salud
Observaciones

El administrador puede:

Crear caballos
Editarlos
Eliminarlos
Consultar todos los caballos

Los usuarios pueden consultar los caballos disponibles.

📅 Sistema de reservas

Los usuarios pueden:

Crear reservas
Consultar reservas futuras
Modificar reservas
Eliminar reservas

Cada reserva contiene:

usuario_id
caballo_id
fecha
hora
comentarios
estado
tipo_pago
estado_pago
📋 Reglas de negocio

El sistema aplica las siguientes condiciones:

Días permitidos

Solo se pueden reservar paseos los sábados y domingos.

Horarios permitidos
10:00
11:00
12:00
13:00
Antelación máxima

Las reservas solo pueden realizarse con un máximo de 30 días de antelación.

Capacidad máxima

Cada turno permite como máximo 5 alumnos.

Caballo único por turno

Un mismo caballo no puede reservarse dos veces en el mismo día y hora.

Caballos enfermos

No se permite reservar caballos marcados como enfermos.

💳 Sistema de pagos

El sistema contempla pago online asociado a la reserva.

Se guarda:

reserva_id
plataforma
cantidad
comision
referencia_pago
estado

La plataforma utilizada en la simulación es Stripe.

Comisión

La comisión puede guardarse junto al pago, permitiendo explicar el coste de la operación.

Ejemplo:

Cantidad: 20.00 €
Comisión: 0.59 €
Plataforma: Stripe
📧 Confirmación por email

Al crear una reserva se envía un email automático al usuario.

Se usa una clase Mailable:

ReservaCreadaMail.php

La configuración se realiza desde .env.

📲 Confirmación por WhatsApp

También se envía una notificación mediante CallMeBot WhatsApp API.

Variables necesarias:

CALLMEBOT_APIKEY=
CALLMEBOT_PHONE=

El mensaje incluye:

Confirmación de reserva
Fecha
Hora
Caballo
👨‍💼 Panel administrador

El sistema incluye rutas protegidas para administración.

El administrador puede:

Gestionar caballos
Ver todas las reservas
Cambiar estados de reservas
Consultar usuarios
Consultar pagos
Consultar estadísticas
Acceder a dashboard
📡 Endpoints principales
Autenticación
Método	Endpoint	Descripción
POST	/api/registro	Registrar usuario
POST	/api/login	Iniciar sesión
POST	/api/logout	Cerrar sesión
Caballos
Método	Endpoint	Descripción
GET	/api/caballos	Listar caballos
GET	/api/caballos/{id}	Ver caballo
Reservas
Método	Endpoint	Descripción
GET	/api/reservas	Listar reservas futuras del usuario
GET	/api/reservas/{id}	Ver una reserva
POST	/api/reservas	Crear reserva
PUT	/api/reservas/{id}	Actualizar reserva
DELETE	/api/reservas/{id}	Eliminar reserva
Disponibilidad y calendario
Método	Endpoint	Descripción
GET	/api/disponibilidad	Consultar caballos disponibles
GET	/api/calendario	Consultar calendario diario
GET	/api/calendario/semana	Consultar calendario semanal
Pagos
Método	Endpoint	Descripción
GET	/api/pagos	Listar pagos del usuario
POST	/api/pagos	Registrar pago
Administración
Método	Endpoint	Descripción
GET	/api/admin/reservas	Ver todas las reservas
GET	/api/admin/pagos	Ver pagos
GET	/api/admin/usuarios	Ver usuarios
GET	/api/admin/estadisticas	Ver estadísticas
GET	/api/admin/dashboard	Dashboard admin
🧪 Ejemplo login
POST /api/login
{
  "email": "ana@test.com",
  "password": "12345678"
}

Respuesta:

{
  "mensaje": "Login correcto",
  "token": "TOKEN",
  "usuario": {
    "id": 1,
    "nombre": "Ana",
    "email": "ana@test.com"
  }
}
🧪 Ejemplo crear reserva
POST /api/reservas

Cabeceras:

Authorization: Bearer TOKEN
Accept: application/json

Body:

{
  "caballo_id": 1,
  "fecha": "2026-05-10",
  "hora": "10:00",
  "comentarios": "Información clásica española"
}
⚙️ Instalación
1. Clonar repositorio
git clone URL_DEL_REPOSITORIO_BACKEND
cd reservas-backend
2. Instalar dependencias
composer install
3. Crear archivo .env
cp .env.example .env
4. Configurar base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Reservas
DB_USERNAME=root
DB_PASSWORD=
5. Generar clave
php artisan key:generate
6. Ejecutar migraciones
php artisan migrate
7. Levantar servidor
php artisan serve
📧 Configuración email

Ejemplo usando Gmail SMTP:

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=correo@gmail.com
MAIL_PASSWORD=clave_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=correo@gmail.com
MAIL_FROM_NAME="Reservas Caballos"
📲 Configuración WhatsApp
CALLMEBOT_APIKEY=TU_APIKEY
CALLMEBOT_PHONE=34TU_NUMERO
🗄️ Base de datos

El proyecto usa MySQL.

La base de datos debe exportarse en formato:

.sql

La codificación recomendada es:

utf8mb4_unicode_ci
🔒 Seguridad

Medidas aplicadas:

Tokens con Sanctum
Rutas protegidas con auth:sanctum
Middleware de administrador
Validación de datos
Contraseñas cifradas con Hash
Respuestas JSON controladas
📱 Integración con Android

La API es consumida por una aplicación Android Kotlin.

La app utiliza:

MVVM
ViewModel
Retrofit
RecyclerView
LiveData
JSON
Bearer Token
✅ Requisitos cumplidos
Reserva online de paseos
Solo fines de semana
Turnos a las 10, 11, 12 y 13
Máximo 5 alumnos por turno
No repetir caballo en el mismo turno
No reservar caballo enfermo
Máximo 30 días de antelación
Gestión de caballos
Usuario administrador
Email de confirmación
WhatsApp de confirmación
Pago online simulado
API REST
Laravel
MySQL
App Android Kotlin
MVVM
Retrofit
🚀 Mejoras futuras
Integración completa con Stripe real
Panel web visual para administradores
Calendario interactivo
Notificaciones push
Recuperación de contraseña
Subida real de imágenes de caballos
Despliegue en VPS con HTTPS
Mejoras de accesibilidad
Test automatizados
👨‍💻 Autor

Eduardo Rodríguez Gallego

Proyecto académico de gestión de reservas para centro ecuestre.
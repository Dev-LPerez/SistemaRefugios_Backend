# SistemaRefugios_Backend

Este es el backend del Sistema de Refugios. Es una API RESTful construida con PHP puro (Vanilla PHP), utilizando un patrón Front Controller (`index.php`) y una arquitectura en capas (Controladores, Servicios y DTOs).

## Arquitectura

El proyecto sigue un patrón modular, donde cada entidad tiene su propio directorio:

- **Controllers:** Manejan las peticiones HTTP (GET, POST, PUT, DELETE) y devuelven respuestas en formato JSON.
- **Services:** Contienen la lógica de negocio y se comunican con la base de datos.
- **DTOs (Data Transfer Objects):** Validan y estructuran los datos de entrada antes de procesarlos.
- **Configuración:** La conexión a la base de datos se realiza en `config/database.php` usando PDO.

## Enrutamiento (URLs Limpias)

Todas las peticiones pasan por el archivo `index.php`, pero el sistema utiliza URLs limpias gracias a la configuración en el archivo `.htaccess`. Esto permite realizar las peticiones de la siguiente manera:

- `/modulo` (Define el controlador y recurso a usar)
- `/modulo/id` (Opcional, para indicar un recurso específico por ID)
- Parámetros adicionales como `?action=valor` o `?id_familia=valor` se pasan como Query Strings normales (`/modulo?parametro=valor`).

Ejemplo general: `GET /refugios`
Ejemplo de un recurso específico: `GET /refugios/1`
Ejemplo compuesto: `GET /familias/miembros/1`

## Módulos y Endpoints Disponibles

### Refugios (`/refugios`)
- `GET /` - Obtener todos los refugios.
- `GET /{id}` - Obtener un refugio específico.
- `POST /` - Crear un nuevo refugio.
- `PUT /{id}` - Actualizar un refugio.
- `DELETE /{id}` - Eliminar un refugio.

### Familias (`/familias`)
- `GET /` - Obtener todas las familias.
- `GET /{id}` - Obtener una familia por ID.
- `POST /` - Registrar una nueva familia.
- `PUT /{id}` - Actualizar datos de una familia.
- `DELETE /{id}` - Eliminar una familia.

### Miembros de Familia (`/familias/miembros`)
- `GET /?id_familia={id}` - Obtener los miembros de una familia específica.
- Admite operaciones estándar CRUD pasando `/familias/miembros/{id}` o `/familias/miembros` en POST.

### Usuarios (`/usuarios`)
- `GET /` - Listar usuarios del sistema.
- `GET /{id}` - Obtener usuario por ID.
- `POST /` - Crear un usuario.
- `PUT /{id}` - Actualizar datos del usuario.
- `DELETE /{id}` - Eliminar usuario.

### Recursos (`/recursos`)
- `GET /`, `POST /`, `PUT /{id}`, `DELETE /{id}`

### Donantes (`/donantes`)
- `GET /`, `POST /`, `PUT /{id}`, `DELETE /{id}`

### Donaciones (`/donaciones`)
- `GET /`, `POST /`, `PUT /{id}`, `DELETE /{id}`
- Admite el parámetro `?action=` para lógicas específicas (ej. agregar detalles de donación).

### Entregas (`/entregas`)
- `GET /`, `POST /`, `PUT /{id}`, `DELETE /{id}`

### Gestiones (`/gestiones`)
- Maneja peticiones de propósito general o de gestión mediante solicitudes a este módulo.

## Instalación y Configuración Local

1. Clona el repositorio en el directorio `htdocs` de XAMPP (o la carpeta equivalente en tu servidor local web).
2. Configura las credenciales de la base de datos en `config/database.php` (`host`, `db_name`, `username`, `password`).
3. Asegúrate de tener habilitado el módulo `mod_rewrite` en Apache para que `.htaccess` funcione correctamente.
4. Inicia Apache y MySQL desde el panel de control de XAMPP.
5. Accede a la API a través de: `http://localhost/Backend_Refugios/` (ej: `http://localhost/Backend_Refugios/refugios`).

## CORS y JSON
La API está configurada para:
- Aceptar peticiones desde cualquier origen (CORS habilitado por defecto: `Access-Control-Allow-Origin: *`).
- Manejar peticiones pre-flight (`OPTIONS`) automáticamente.
- Recibir datos mediante el body en formato JSON (`php://input`) y devolver respuestas con la cabecera `Content-Type: application/json`.

## Listado Completo de Peticiones API (GET, POST, PUT, DELETE)

1. Módulo: Refugios
Listar todos los refugios:
http://localhost/Backend_Refugios/refugios

Ver un refugio específico (por ID):
http://localhost/Backend_Refugios/refugios/1

Crear un nuevo refugio (POST) / Actualizar un refugio existente (PUT):
http://localhost/Backend_Refugios/refugios
http://localhost/Backend_Refugios/refugios/1

**Ejemplo de JSON (Body):**
```json
{
  "nombre": "Refugio Central",
  "ubicacion": "Centro de la Ciudad",
  "capacidad": 50
}
```

Eliminar un refugio:
http://localhost/Backend_Refugios/refugios/1

---

2. Módulo: Familias
Listar todas las familias:
http://localhost/Backend_Refugios/familias

Ver una familia específica (por ID):
http://localhost/Backend_Refugios/familias/1

Registrar una nueva familia (POST) / Actualizar datos de una familia (PUT):
http://localhost/Backend_Refugios/familias
http://localhost/Backend_Refugios/familias/1

**Ejemplo de JSON (Body):**
```json
{
  "representante": "Juan Perez",
  "telefono": "0987654321",
  "direccion": "Av. Principal 123",
  "cantidad_miembros": 4,
  "prioridad": "Alta",
  "id_refugio": 1
}
```

Eliminar una familia:
http://localhost/Backend_Refugios/familias/1

---

3. Módulo: Miembros de Familia
Listar los miembros de una familia:
http://localhost/Backend_Refugios/familias/miembros?id_familia=1

Ver un miembro específico:
http://localhost/Backend_Refugios/familias/miembros/1

Agregar un nuevo miembro (POST) / Actualizar un miembro (PUT):
http://localhost/Backend_Refugios/familias/miembros
http://localhost/Backend_Refugios/familias/miembros/1

**Ejemplo de JSON (Body):**
```json
{
  "nombre": "Maria Perez",
  "edad": 12,
  "parentezco": "Hija",
  "tipo_documento": "Cedula",
  "numero_documento": "1234567890",
  "vulnerable": 1,
  "tipo_vulnerabilidad": "Menor de edad",
  "id_familia": 1
}
```

Eliminar un miembro:
http://localhost/Backend_Refugios/familias/miembros/1

---

4. Módulo: Usuarios
Listar todos los usuarios:
http://localhost/Backend_Refugios/usuarios

Ver un usuario específico:
http://localhost/Backend_Refugios/usuarios/1

Crear un nuevo usuario (POST) / Actualizar un usuario (PUT):
http://localhost/Backend_Refugios/usuarios
http://localhost/Backend_Refugios/usuarios/1

**Ejemplo de JSON (Body):**
```json
{
  "user": "admin",
  "password": "password123",
  "rol": "Administrador"
}
```

Eliminar un usuario:
http://localhost/Backend_Refugios/usuarios/1

---

5. Módulo: Recursos
Listar todos los recursos:
http://localhost/Backend_Refugios/recursos

Ver un recurso específico:
http://localhost/Backend_Refugios/recursos/1

Crear un nuevo recurso (POST) / Actualizar un recurso (PUT):
http://localhost/Backend_Refugios/recursos
http://localhost/Backend_Refugios/recursos/1

**Ejemplo de JSON (Body):**
```json
{
  "nombre": "Agua embotellada",
  "tipo": "Alimento",
  "unidad": "Litros",
  "cantidad_disponible": 100
}
```

Eliminar un recurso:
http://localhost/Backend_Refugios/recursos/1

---

6. Módulo: Donantes
Listar todos los donantes:
http://localhost/Backend_Refugios/donantes

Ver un donante específico:
http://localhost/Backend_Refugios/donantes/1

Crear un nuevo donante (POST) / Actualizar un donante (PUT):
http://localhost/Backend_Refugios/donantes
http://localhost/Backend_Refugios/donantes/1

**Ejemplo de JSON (Body):**
```json
{
  "identificacion": "0987654321",
  "nombre": "Empresa Solidaria SA",
  "tipo": "Juridico",
  "telefono": "0991234567"
}
```

Eliminar un donante:
http://localhost/Backend_Refugios/donantes/1

---

7. Módulo: Donaciones
Listar todas las donaciones:
http://localhost/Backend_Refugios/donaciones

Ver una donación específica:
http://localhost/Backend_Refugios/donaciones/1

Crear una nueva donación (POST) / Actualizar una donación (PUT):
http://localhost/Backend_Refugios/donaciones
http://localhost/Backend_Refugios/donaciones/1

**Ejemplo de JSON (Body):**
```json
{
  "fecha": "2023-10-25",
  "descripcion": "Donacion de agua y viveres",
  "id_donante": 1
}
```

Eliminar una donación:
http://localhost/Backend_Refugios/donaciones/1

---

8. Módulo: Entregas
Listar todas las entregas:
http://localhost/Backend_Refugios/entregas

Ver una entrega específica:
http://localhost/Backend_Refugios/entregas/1

Crear una nueva entrega (POST) / Actualizar una entrega (PUT):
http://localhost/Backend_Refugios/entregas
http://localhost/Backend_Refugios/entregas/1

**Ejemplo de JSON (Body):**
```json
{
  "estado": "Entregado",
  "fecha": "2023-10-26",
  "cantidad": 5.5,
  "id_familia": 1,
  "id_recurso": 2
}
```

Eliminar una entrega:
http://localhost/Backend_Refugios/entregas/1

---

9. Módulo: Gestiones
Realizar peticiones de gestiones y reportes (POST):
http://localhost/Backend_Refugios/gestiones

**Ejemplo de JSON (Body para POST):**
```json
{
  "id_usuario": 1,
  "id_recurso": 2,
  "accion": "Ingreso de nuevo lote"
}
```

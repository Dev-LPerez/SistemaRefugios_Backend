# SistemaRefugios_Backend

Este es el backend del Sistema de Refugios. Es una API RESTful construida con PHP puro (Vanilla PHP), utilizando un patrón Front Controller (`index.php`) y una arquitectura en capas (Controladores, Servicios y DTOs).

## Arquitectura

El proyecto sigue un patrón modular, donde cada entidad tiene su propio directorio:

- **Controllers:** Manejan las peticiones HTTP (GET, POST, PUT, DELETE) y devuelven respuestas en formato JSON.
- **Services:** Contienen la lógica de negocio y se comunican con la base de datos.
- **DTOs (Data Transfer Objects):** Validan y estructuran los datos de entrada antes de procesarlos.
- **Configuración:** La conexión a la base de datos se realiza en `config/database.php` usando PDO.

## Enrutamiento (Front Controller)

Todas las peticiones pasan por el archivo `index.php`, el cual intercepta y enruta la solicitud al controlador correspondiente utilizando parámetros en la URL:

- `?route=modulo` (Define el controlador a usar)
- `?id=valor` (Opcional, para indicar un recurso específico)
- `?action=valor` (Opcional, para acciones específicas)
- `?id_familia=valor` (Opcional, filtro usado en miembros de familia)

Ejemplo general: `GET /index.php?route=refugios`
Ejemplo de un recurso específico: `GET /index.php?route=refugios&id=1`

## Módulos y Endpoints Disponibles

### Refugios (`?route=refugios`)
- `GET /` - Obtener todos los refugios.
- `GET /&id={id}` - Obtener un refugio específico.
- `POST /` - Crear un nuevo refugio.
- `PUT /&id={id}` - Actualizar un refugio.
- `DELETE /&id={id}` - Eliminar un refugio.

### Familias (`?route=familias`)
- `GET /` - Obtener todas las familias.
- `GET /&id={id}` - Obtener una familia por ID.
- `POST /` - Registrar una nueva familia.
- `PUT /&id={id}` - Actualizar datos de una familia.
- `DELETE /&id={id}` - Eliminar una familia.

### Miembros de Familia (`?route=familias/miembros`)
- `GET /&id_familia={id}` - Obtener los miembros de una familia.
- Admite operaciones estándar CRUD pasando `?route=familias/miembros` e `&id={id}` o `POST /`.

### Usuarios (`?route=usuarios`)
- `GET /` - Listar usuarios del sistema.
- `GET /&id={id}` - Obtener usuario por ID.
- `POST /` - Crear un usuario.
- `PUT /&id={id}` - Actualizar datos del usuario.
- `DELETE /&id={id}` - Eliminar usuario.

### Recursos (`?route=recursos`)
- `GET /`, `POST /`, `PUT /&id={id}`, `DELETE /&id={id}`

### Donantes (`?route=donantes`)
- `GET /`, `POST /`, `PUT /&id={id}`, `DELETE /&id={id}`

### Donaciones (`?route=donaciones`)
- `GET /`, `POST /`, `PUT /&id={id}`, `DELETE /&id={id}`
- Admite el parámetro `&action=` para lógicas específicas (ej. agregar detalles de donación).

### Entregas (`?route=entregas`)
- `GET /`, `POST /`, `PUT /&id={id}`, `DELETE /&id={id}`

### Gestiones (`?route=gestiones`)
- Maneja peticiones de propósito general o de gestión mediante solicitudes a este módulo.

## Instalación y Configuración Local

1. Clona el repositorio en el directorio `htdocs` de XAMPP (o la carpeta equivalente en tu servidor local web).
2. Configura las credenciales de la base de datos en `config/database.php` (`host`, `db_name`, `username`, `password`).
3. Inicia Apache y MySQL desde el panel de control de XAMPP.
4. Accede a la API a través de: `http://localhost/Backend_Refugios/index.php`.

## CORS y JSON
La API está configurada para:
- Aceptar peticiones desde cualquier origen (CORS habilitado por defecto: `Access-Control-Allow-Origin: *`).
- Manejar peticiones pre-flight (`OPTIONS`) automáticamente.
- Recibir datos mediante el body en formato JSON (`php://input`) y devolver respuestas con la cabecera `Content-Type: application/json`.

## Listado Completo de Peticiones API (GET, POST, PUT, DELETE)

1. Módulo: Refugios
Listar todos los refugios:
http://localhost/Backend_Refugios/index.php?route=refugios

Ver un refugio específico (por ID):
http://localhost/Backend_Refugios/index.php?route=refugios&id=1

Crear un nuevo refugio (POST) / Actualizar un refugio existente (PUT):
http://localhost/Backend_Refugios/index.php?route=refugios
http://localhost/Backend_Refugios/index.php?route=refugios&id=1

**Ejemplo de JSON (Body):**
```json
{
  "nombre": "Refugio Central",
  "ubicacion": "Centro de la Ciudad",
  "capacidad": 50
}
```

Eliminar un refugio:
http://localhost/Backend_Refugios/index.php?route=refugios&id=1

---

2. Módulo: Familias
Listar todas las familias:
http://localhost/Backend_Refugios/index.php?route=familias

Ver una familia específica (por ID):
http://localhost/Backend_Refugios/index.php?route=familias&id=1

Registrar una nueva familia (POST) / Actualizar datos de una familia (PUT):
http://localhost/Backend_Refugios/index.php?route=familias
http://localhost/Backend_Refugios/index.php?route=familias&id=1

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
http://localhost/Backend_Refugios/index.php?route=familias&id=1

---

3. Módulo: Miembros de Familia
Listar los miembros de una familia:
http://localhost/Backend_Refugios/index.php?route=familias/miembros&id_familia=1

Ver un miembro específico:
http://localhost/Backend_Refugios/index.php?route=familias/miembros&id=1

Agregar un nuevo miembro (POST) / Actualizar un miembro (PUT):
http://localhost/Backend_Refugios/index.php?route=familias/miembros
http://localhost/Backend_Refugios/index.php?route=familias/miembros&id=1

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
http://localhost/Backend_Refugios/index.php?route=familias/miembros&id=1

---

4. Módulo: Usuarios
Listar todos los usuarios:
http://localhost/Backend_Refugios/index.php?route=usuarios

Ver un usuario específico:
http://localhost/Backend_Refugios/index.php?route=usuarios&id=1

Crear un nuevo usuario (POST) / Actualizar un usuario (PUT):
http://localhost/Backend_Refugios/index.php?route=usuarios
http://localhost/Backend_Refugios/index.php?route=usuarios&id=1

**Ejemplo de JSON (Body):**
```json
{
  "user": "admin",
  "password": "password123",
  "rol": "Administrador"
}
```

Eliminar un usuario:
http://localhost/Backend_Refugios/index.php?route=usuarios&id=1

---

5. Módulo: Recursos
Listar todos los recursos:
http://localhost/Backend_Refugios/index.php?route=recursos

Ver un recurso específico:
http://localhost/Backend_Refugios/index.php?route=recursos&id=1

Crear un nuevo recurso (POST) / Actualizar un recurso (PUT):
http://localhost/Backend_Refugios/index.php?route=recursos
http://localhost/Backend_Refugios/index.php?route=recursos&id=1

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
http://localhost/Backend_Refugios/index.php?route=recursos&id=1

---

6. Módulo: Donantes
Listar todos los donantes:
http://localhost/Backend_Refugios/index.php?route=donantes

Ver un donante específico:
http://localhost/Backend_Refugios/index.php?route=donantes&id=1

Crear un nuevo donante (POST) / Actualizar un donante (PUT):
http://localhost/Backend_Refugios/index.php?route=donantes
http://localhost/Backend_Refugios/index.php?route=donantes&id=1

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
http://localhost/Backend_Refugios/index.php?route=donantes&id=1

---

7. Módulo: Donaciones
Listar todas las donaciones:
http://localhost/Backend_Refugios/index.php?route=donaciones

Ver una donación específica:
http://localhost/Backend_Refugios/index.php?route=donaciones&id=1

Crear una nueva donación (POST) / Actualizar una donación (PUT):
http://localhost/Backend_Refugios/index.php?route=donaciones
http://localhost/Backend_Refugios/index.php?route=donaciones&id=1

**Ejemplo de JSON (Body):**
```json
{
  "fecha": "2023-10-25",
  "descripcion": "Donacion de agua y viveres",
  "id_donante": 1
}
```

Eliminar una donación:
http://localhost/Backend_Refugios/index.php?route=donaciones&id=1

---

8. Módulo: Entregas
Listar todas las entregas:
http://localhost/Backend_Refugios/index.php?route=entregas

Ver una entrega específica:
http://localhost/Backend_Refugios/index.php?route=entregas&id=1

Crear una nueva entrega (POST) / Actualizar una entrega (PUT):
http://localhost/Backend_Refugios/index.php?route=entregas
http://localhost/Backend_Refugios/index.php?route=entregas&id=1

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
http://localhost/Backend_Refugios/index.php?route=entregas&id=1

---

9. Módulo: Gestiones
Realizar peticiones de gestiones y reportes (POST):
http://localhost/Backend_Refugios/index.php?route=gestiones

**Ejemplo de JSON (Body para POST):**
```json
{
  "id_usuario": 1,
  "id_recurso": 2,
  "accion": "Ingreso de nuevo lote"
}
```

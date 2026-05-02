
---

### 1. Módulo: Refugios
*   **Listar todos:** `GET https://sistemarefugios-backend.onrender.com/refugios`
*   **Ver uno solo:** `GET https://sistemarefugios-backend.onrender.com/refugios/1`
*   **Crear (POST):** `POST https://sistemarefugios-backend.onrender.com/refugios`
    ```json
    {
      "nombre": "Refugio La Esperanza",
      "ubicacion": "Sector La Castellana",
      "capacidad": 150
    }
    ```
*   **Editar (PUT):** `PUT https://sistemarefugios-backend.onrender.com/refugios/1`
    ```json
    {
      "nombre": "Refugio La Esperanza",
      "ubicacion": "Sector La Castellana",
      "capacidad": 200
    }
    ```
*   **Eliminar:** `DELETE https://sistemarefugios-backend.onrender.com/refugios/1`

### 2. Módulo: Familias
*   **Listar todas:** `GET https://sistemarefugios-backend.onrender.com/familias`
*   **Ver una sola:** `GET https://sistemarefugios-backend.onrender.com/familias/1`
*   **Crear (POST):** `POST https://sistemarefugios-backend.onrender.com/familias`
    ```json
    {
      "representante": "Andrea Martinez",
      "telefono": "3009998888",
      "direccion": "Barrio El Prado",
      "cantidad_miembros": 3,
      "prioridad": "alta",
      "id_refugio": 1
    }
    ```
*   **Editar (PUT):** `PUT https://sistemarefugios-backend.onrender.com/familias/1`
    ```json
    {
      "representante": "Andrea Martinez",
      "telefono": "3009998888",
      "direccion": "Barrio El Prado",
      "cantidad_miembros": 4,
      "prioridad": "alta",
      "id_refugio": 1
    }
    ```
*   **Eliminar:** `DELETE https://sistemarefugios-backend.onrender.com/familias/1`

### 3. Módulo: Miembros (Personas)
*   **Listar todos los de una familia:** `GET https://sistemarefugios-backend.onrender.com/familias/miembros?id_familia=1`
*   **Ver un miembro específico:** `GET https://sistemarefugios-backend.onrender.com/familias/miembros/1`
*   **Agregar miembro a familia (POST):** `POST https://sistemarefugios-backend.onrender.com/familias/miembros`
    ```json
    {
      "nombre": "Sofia Martinez",
      "edad": 8,
      "parentezco": "hija",
      "tipo_documento": "TI",
      "numero_documento": "100200300",
      "vulnerable": 1,
      "tipo_vulnerabilidad": "niño",
      "id_familia": 1
    }
    ```
*   **Editar miembro (PUT):** `PUT https://sistemarefugios-backend.onrender.com/familias/miembros/1`
    ```json
    {
      "nombre": "Sofia Martinez",
      "edad": 9,
      "parentezco": "hija",
      "tipo_documento": "TI",
      "numero_documento": "100200300",
      "vulnerable": 1,
      "tipo_vulnerabilidad": "niño",
      "id_familia": 1
    }
    ```
*   **Eliminar:** `DELETE https://sistemarefugios-backend.onrender.com/familias/miembros/1`

### 4. Módulo: Recursos (Inventario)
*   **Ver todo el inventario:** `GET https://sistemarefugios-backend.onrender.com/recursos`
*   **Ver un recurso:** `GET https://sistemarefugios-backend.onrender.com/recursos/1`
*   **Crear un artículo nuevo (POST):** `POST https://sistemarefugios-backend.onrender.com/recursos`
    ```json
    {
      "nombre": "Kit de Primeros Auxilios",
      "tipo": "salud",
      "unidad": "cajas",
      "cantidad_disponible": 0
    }
    ```
*   **Editar un recurso (PUT):** `PUT https://sistemarefugios-backend.onrender.com/recursos/1`
    ```json
    {
      "nombre": "Kit de Primeros Auxilios",
      "tipo": "salud",
      "unidad": "cajas",
      "cantidad_disponible": 10
    }
    ```
*   **Eliminar:** `DELETE https://sistemarefugios-backend.onrender.com/recursos/1`

### 5. Módulo: Donantes
*   **Listar todos:** `GET https://sistemarefugios-backend.onrender.com/donantes`
*   **Ver uno:** `GET https://sistemarefugios-backend.onrender.com/donantes/1`
*   **Crear donante (POST):** `POST https://sistemarefugios-backend.onrender.com/donantes`
    ```json
    {
      "identificacion": "800900100",
      "nombre": "Fundación Vida",
      "tipo": "ONG",
      "telefono": "3120000000"
    }
    ```
*   **Editar donante (PUT):** `PUT https://sistemarefugios-backend.onrender.com/donantes/1`
    ```json
    {
      "identificacion": "800900100",
      "nombre": "Fundación Nueva Vida",
      "tipo": "ONG",
      "telefono": "3120000000"
    }
    ```
*   **Eliminar:** `DELETE https://sistemarefugios-backend.onrender.com/donantes/1`

### 6. Módulo: Donaciones (Entradas de Stock)
*   **Ver una donación con sus detalles:** `GET https://sistemarefugios-backend.onrender.com/donaciones/1`
*   **Listar donaciones:** `GET https://sistemarefugios-backend.onrender.com/donaciones`
*   **Paso A - Crear Cabecera (POST):** `POST https://sistemarefugios-backend.onrender.com/donaciones`
    ```json
    {
      "fecha": "2026-05-02",
      "descripcion": "Lote de medicinas y mantas",
      "id_donante": 1
    }
    ```
*   **Paso B - Agregar Recursos a esa Donación (POST):** `POST https://sistemarefugios-backend.onrender.com/donaciones?action=agregar_detalle`
    ```json
    {
      "id_donacion": 1,
      "id_recurso": 2,
      "cantidad": 50
    }
    ```
*   **Editar donación (PUT):** `PUT https://sistemarefugios-backend.onrender.com/donaciones/1`
    ```json
    {
      "fecha": "2026-05-02",
      "descripcion": "Lote actualizado de medicinas",
      "id_donante": 1
    }
    ```
*   **Eliminar:** `DELETE https://sistemarefugios-backend.onrender.com/donaciones/1`

### 7. Módulo: Entregas (Salidas de Stock)
*   **Ver historial general:** `GET https://sistemarefugios-backend.onrender.com/entregas`
*   **Ver una entrega:** `GET https://sistemarefugios-backend.onrender.com/entregas/1`
*   **Entregar recurso a familia (POST):** `POST https://sistemarefugios-backend.onrender.com/entregas`
    ```json
    {
      "estado": "entregado",
      "fecha": "2026-05-02",
      "cantidad": 2,
      "id_familia": 1,
      "id_recurso": 2
    }
    ```
*   **Editar entrega (PUT):** `PUT https://sistemarefugios-backend.onrender.com/entregas/1`
    ```json
    {
      "estado": "entregado",
      "fecha": "2026-05-02",
      "cantidad": 3,
      "id_familia": 1,
      "id_recurso": 2
    }
    ```
*   **Eliminar:** `DELETE https://sistemarefugios-backend.onrender.com/entregas/1`

### 8. Módulo: Gestiones (Auditoría manual)
*   **Ver historial:** `GET https://sistemarefugios-backend.onrender.com/gestiones`
*   **Ver una gestión:** `GET https://sistemarefugios-backend.onrender.com/gestiones/1`
*   **Registrar movimiento (POST):** `POST https://sistemarefugios-backend.onrender.com/gestiones`
    ```json
    {
      "id_usuario": 1,
      "id_recurso": 1,
      "accion": "Ajuste de inventario por merma"
    }
    ```
*   **Editar movimiento (PUT):** `PUT https://sistemarefugios-backend.onrender.com/gestiones/1`
    ```json
    {
      "id_usuario": 1,
      "id_recurso": 1,
      "accion": "Ajuste de inventario por merma corregido"
    }
    ```
*   **Eliminar:** `DELETE https://sistemarefugios-backend.onrender.com/gestiones/1`

### 9. Módulo: Usuarios
*   **Listar todos:** `GET https://sistemarefugios-backend.onrender.com/usuarios`
*   **Ver un usuario:** `GET https://sistemarefugios-backend.onrender.com/usuarios/1`
*   **Crear nuevo (POST):** `POST https://sistemarefugios-backend.onrender.com/usuarios`
    ```json
    {
      "user": "juan_coordinador",
      "password": "super_secret_password",
      "rol": "coordinador"
    }
    ```
*   **Editar usuario (PUT):** `PUT https://sistemarefugios-backend.onrender.com/usuarios/1`
    ```json
    {
      "user": "juan_coordinador",
      "password": "new_super_secret_password",
      "rol": "admin"
    }
    ```
*   **Eliminar:** `DELETE https://sistemarefugios-backend.onrender.com/usuarios/1`
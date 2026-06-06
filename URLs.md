# API Refugios — Guía de Pruebas en Postman

**Base URL local:** `http://localhost/Backend_Refugios`  
**Base URL producción:** `https://sistemarefugios-backend.onrender.com`

> Todos los endpoints reciben y devuelven **JSON**.  
> Los endpoints protegidos requieren el header: `Authorization: Bearer {token}`

---

## Flujo de Prueba Recomendado

Sigue este orden para respetar las dependencias entre módulos:

```
1. Crear Usuario → 2. Login → 3. Crear Refugio → 4. Crear Familia →
5. Crear Miembro → 6. Crear Recurso → 7. Crear Donante →
8. Crear Donación → 9. Agregar Detalle → 10. Crear Entrega → 11. Probar bloqueo stock
```

---

## 1. Usuarios

### Crear Usuario
```
POST /usuarios
```
```json
{
  "user": "admin_test",
  "password": "1234",
  "rol": "Admin"
}
```
**Respuesta esperada:** `201` — `"Usuario creado exitosamente."`

---

### Listar Usuarios
```
GET /usuarios
```

### Ver Usuario por ID
```
GET /usuarios/1
```

### Actualizar Usuario
```
PUT /usuarios/1
```
```json
{
  "user": "admin_actualizado",
  "password": "nueva_pass",
  "rol": "Admin"
}
```

### Eliminar Usuario
```
DELETE /usuarios/1
```

---

## 2. Login / Autenticación

### Login
```
POST /login
```
```json
{
  "user": "admin_test",
  "password": "1234"
}
```
**Respuesta esperada:** `200` + token JWT.  
> Guarda el token para usarlo en los endpoints protegidos como header: `Authorization: Bearer {token}`

---

## 3. Refugios

### Crear Refugio
```
POST /refugios
```
```json
{
  "nombre": "Refugio Norte",
  "direccion": "Calle 10 #5-20",
  "capacidad_maxima": 50,
  "estado": "activo"
}
```
**Respuesta esperada:** `201` — Guarda el `id_refugio`.

---

### Listar Refugios
```
GET /refugios
```

### Ver Refugio por ID
```
GET /refugios/1
```

### Actualizar Refugio
```
PUT /refugios/1
```
```json
{
  "nombre": "Refugio Norte Actualizado",
  "direccion": "Calle 10 #5-20",
  "capacidad_maxima": 80,
  "ocupacion_actual": 10,
  "estado": "activo"
}
```

### Eliminar Refugio
```
DELETE /refugios/1
```

---

## 4. Familias

### Registrar Familia
```
POST /familias
```
```json
{
  "cedula": "12345678",
  "representante": "Juan Pérez",
  "telefono": "3001234567",
  "direccion": "Sector La Planicie, Calle 4, Casa 12",
  "cantidad_miembros": 4,
  "prioridad": "Alta",
  "id_refugio": 1,
  "ubicacion_actual": "Refugio",
  "aceptacion_habeas_data": true
}
```
**Respuesta esperada:** `201` — Guarda el `id_familia`.

---

### Listar Familias
```
GET /familias
```

### Ver Familia por ID
```
GET /familias/1
```

### Buscar por Cédula
```
GET /familias?cedula=12345678
```

### Actualizar Familia
```
PUT /familias/1
```
```json
{
  "cedula": "12345678",
  "representante": "Juan Pérez",
  "telefono": "3009999999",
  "direccion": "Nueva dirección",
  "cantidad_miembros": 5,
  "prioridad": "Alta",
  "id_refugio": 1,
  "ubicacion_actual": "Refugio",
  "aceptacion_habeas_data": true
}
```

### Eliminar Familia
```
DELETE /familias/1
```

---

## 5. Miembros de Familia

### Agregar Miembro
```
POST /familias/miembros
```
```json
{
  "nombre": "Maria Pérez",
  "edad": 8,
  "es_embarazada": false,
  "tiene_discapacidad": false,
  "enfermedad_cronica": false,
  "parentezco": "Hija",
  "tipo_documento": "TI",
  "numero_documento": "987654321",
  "vulnerable": 1,
  "tipo_vulnerabilidad": "Menor de edad",
  "id_familia": 1
}
```
**Respuesta esperada:** `201`

---

### Listar Miembros de una Familia
```
GET /familias/miembros?id_familia=1
```

### Ver Miembro por ID
```
GET /familias/miembros/1
```

### Actualizar Miembro
```
PUT /familias/miembros/1
```
```json
{
  "nombre": "Maria Pérez",
  "edad": 9,
  "es_embarazada": false,
  "tiene_discapacidad": false,
  "enfermedad_cronica": false,
  "parentezco": "Hija",
  "tipo_documento": "TI",
  "numero_documento": "987654321",
  "vulnerable": 1,
  "tipo_vulnerabilidad": "Menor de edad",
  "id_familia": 1
}
```

### Eliminar Miembro
```
DELETE /familias/miembros/1
```

---

## 6. Recursos (Inventario)

### Crear Recurso
```
POST /recursos
```
```json
{
  "nombre": "Agua embotellada",
  "tipo": "Alimento",
  "unidad": "Litros",
  "cantidad_disponible": 0
}
```
**Respuesta esperada:** `201` — Guarda el `id_recurso`.

---

### Ver Todo el Inventario
```
GET /recursos
```

### Ver Recurso por ID
```
GET /recursos/1
```

### Actualizar Recurso
```
PUT /recursos/1
```
```json
{
  "nombre": "Agua embotellada",
  "tipo": "Alimento",
  "unidad": "Litros",
  "cantidad_disponible": 200
}
```

### Eliminar Recurso
```
DELETE /recursos/1
```

---

## 7. Donantes

### Crear Donante
```
POST /donantes
```
```json
{
  "identificacion": "9001234567",
  "nombre": "Empresa Solidaria SAS",
  "tipo": "empresa",
  "telefono": "3100000000"
}
```
**Respuesta esperada:** `201` — Guarda el `id_donante`.

---

### Listar Donantes
```
GET /donantes
```

### Ver Donante por ID
```
GET /donantes/1
```

### Actualizar Donante
```
PUT /donantes/1
```
```json
{
  "identificacion": "9001234567",
  "nombre": "Empresa Solidaria SAS Actualizada",
  "tipo": "empresa",
  "telefono": "3109999999"
}
```

### Eliminar Donante
```
DELETE /donantes/1
```

---

## 8. Donaciones

### Paso A — Crear Cabecera de Donación
```
POST /donaciones
```
```json
{
  "fecha": "2026-05-14",
  "descripcion": "Donación de agua y alimentos",
  "id_donante": 1
}
```
**Respuesta esperada:** `201` — Guarda el `id_donacion`.

---

### Paso B — Agregar Detalle (incrementa inventario automáticamente)
```
POST /donaciones?action=agregar_detalle
```
```json
{
  "id_donacion": 1,
  "id_recurso": 1,
  "cantidad": 100
}
```
**Respuesta esperada:** `201` — Verifica con `GET /recursos/1` que `cantidad_disponible` subió.

---

### Listar Donaciones
```
GET /donaciones
```

### Ver Donación por ID
```
GET /donaciones/1
```

### Actualizar Donación
```
PUT /donaciones/1
```
```json
{
  "fecha": "2026-05-14",
  "descripcion": "Descripción actualizada",
  "id_donante": 1
}
```

### Eliminar Donación
```
DELETE /donaciones/1
```

---

## 9. Entregas

### Registrar Entrega - Un solo recurso (descuenta inventario automáticamente)
```
POST /entregas
```
```json
{
  "estado": "entregado",
  "fecha": "2026-05-14",
  "cantidad": 20,
  "id_familia": 1,
  "id_recurso": 1
}
```
**Respuesta esperada:** `201` — Verifica con `GET /recursos/1` que `cantidad_disponible` bajó.

---

### Registrar Entrega - Múltiples recursos (NUEVO)
```
POST /entregas
```
```json
{
  "estado": "entregado",
  "fecha": "2026-05-14",
  "id_familia": 1,
  "recursos": [
    {
      "id_recurso": 1,
      "cantidad": 20
    },
    {
      "id_recurso": 2,
      "cantidad": 5
    }
  ]
}
```
**Respuesta esperada:** `201` — Verifica con `GET /recursos/1` y `GET /recursos/2` que el stock disminuyó para ambos.

---

### Prueba: Stock Insuficiente
```
POST /entregas
```
```json
{
  "estado": "entregado",
  "fecha": "2026-05-14",
  "cantidad": 9999,
  "id_familia": 2,
  "id_recurso": 1
}
```
**Respuesta esperada:** `400` — `"Stock insuficiente. Solo quedan X de Y"`

---

### Prueba: Bloqueo de 72 horas (RF-05.02)
Intenta hacer una segunda entrega a la misma familia el mismo día:
```
POST /entregas
```
```json
{
  "estado": "entregado",
  "fecha": "2026-05-14",
  "cantidad": 5,
  "id_familia": 1,
  "id_recurso": 1
}
```
**Respuesta esperada:** `400` — `"Bloqueo de seguridad: La familia recibió apoyo hace menos de 72 horas..."`

---

### Listar Entregas
```
GET /entregas
```

### Ver Entrega por ID
```
GET /entregas/1
```

### Eliminar Entrega (devuelve stock automáticamente)
```
DELETE /entregas/1
```
**Respuesta esperada:** `200` — `"Entrega eliminada. El inventario ha sido devuelto."` — Verifica que el stock subió.

---

## 10. Gestiones

### Registrar Gestión
```
POST /gestiones
```
```json
{
  "id_usuario": 1,
  "id_recurso": 1,
  "accion": "Ajuste de inventario por merma"
}
```

### Listar Gestiones
```
GET /gestiones
```

### Ver Gestión por ID
```
GET /gestiones/1
```

### Actualizar Gestión
```
PUT /gestiones/1
```
```json
{
  "id_usuario": 1,
  "id_recurso": 1,
  "accion": "Ajuste de inventario corregido"
}
```

### Eliminar Gestión
```
DELETE /gestiones/1
```

---

## 11. Priorización

### Generar Lista de Despacho (RF-04.03)
```
GET /priorizacion
```
**Respuesta:** Lista de todas las familias ordenadas de mayor a menor puntaje de prioridad, con su ración de supervivencia calculada.

### Calcular Puntaje de una Familia (RF-04.02)
```
GET /priorizacion?id_familia=1
```

---

## 12. Reportes

### Trazabilidad Origen-Destino (RF-06.01)
```
GET /reportes?action=trazabilidad
```
**Respuesta:** Qué familias recibieron qué recursos y de qué donantes provienen.

### Dashboard de Estadísticas (RF-06.02)
```
GET /reportes?action=dashboard
```
**Respuesta:** Total familias, personas, entregas completadas, unidades en almacén y recursos en alerta de stock.

---

## 13. Auditoría

### Ver Logs de Auditoría (RNF-05.03)
```
GET /auditoria
```
**Respuesta:** Historial completo de acciones con usuario, acción, entidad, IP y timestamp.

---

## Resumen de Endpoints

| Módulo | GET all | GET id | POST | PUT | DELETE |
|---|---|---|---|---|---|
| Refugios | `/refugios` | `/refugios/{id}` | `/refugios` | `/refugios/{id}` | `/refugios/{id}` |
| Familias | `/familias` | `/familias/{id}` | `/familias` | `/familias/{id}` | `/familias/{id}` |
| Miembros | `/familias/miembros?id_familia={id}` | `/familias/miembros/{id}` | `/familias/miembros` | `/familias/miembros/{id}` | `/familias/miembros/{id}` |
| Usuarios | `/usuarios` | `/usuarios/{id}` | `/usuarios` | `/usuarios/{id}` | `/usuarios/{id}` |
| Recursos | `/recursos` | `/recursos/{id}` | `/recursos` | `/recursos/{id}` | `/recursos/{id}` |
| Donantes | `/donantes` | `/donantes/{id}` | `/donantes` | `/donantes/{id}` | `/donantes/{id}` |
| Donaciones | `/donaciones` | `/donaciones/{id}` | `/donaciones` | `/donaciones/{id}` | `/donaciones/{id}` |
| Detalle Donación | — | — | `/donaciones?action=agregar_detalle` | — | — |
| Entregas | `/entregas` | `/entregas/{id}` | `/entregas` | — | `/entregas/{id}` |
| Gestiones | `/gestiones` | `/gestiones/{id}` | `/gestiones` | `/gestiones/{id}` | `/gestiones/{id}` |
| Priorización | `/priorizacion` | `/priorizacion?id_familia={id}` | — | — | — |
| Reportes | `/reportes?action=dashboard` | `/reportes?action=trazabilidad` | — | — | — |
| Auditoría | `/auditoria` | — | — | — | — |
| Login | — | — | `/login` | — | — |
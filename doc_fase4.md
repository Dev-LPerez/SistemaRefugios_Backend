# Fase 4: Construcción del Motor de Priorización (Completada)

## Objetivos Alcanzados
En esta fase nos centramos en las reglas de negocio sobre cómo repartir inteligentemente los recursos, dando peso a familias en situación de vulnerabilidad y definiendo de forma programática las raciones a dispersar.

1. **Creación del Módulo Priorización**:
   - `src/priorizacion/controller/PriorizacionController.php`
   - `src/priorizacion/service/MotorPriorizacionService.php`

2. **Algoritmo de Ración de Supervivencia**:
   - Para un margen estándar de **3 días** se determina teóricamente que cada individuo consume:
     - 2 litros de agua por día.
     - 1.5 kilos de alimentos por día.
   - El servicio toma el número total de miembros por familia y devuelve un objeto que proyecta los `dias_cobertura`, `agua_litros` y `alimentos_kilos`.

3. **Algoritmo de Puntaje (Baremación)**:
   - Toda familia gana **10 puntos base**.
   - Por iteración de miembros de la familia que se encuentren entre 0 - 5 años: **+5 puntos**.
   - Por personas en rango mayor a 65 años: **+5 puntos**.
   - Si la persona tiene condición de `vulnerable=1` (embarazo, discapacidad u otros): **+10 puntos**. 

4. **Motor de Despachos**:
   - Se configuró el cruce entre familias, el tamaño del núcleo para proyectar ración y su baremo.
   - Usando la función `usort` se devuelve la respuesta priorizando el puntaje en escala descendente (los más críticos hacia arriba).

5. **Nuevos Endpoints**:
   - La nueva ruta preasignada y segura en `index.php` es `?route=priorizacion`.
   - `GET /api?route=priorizacion&action=despachos`: Muestra lista final del cruce ordenado para enviar a la capa de Logística de Operarios Terrenales.
   - `POST /api?route=priorizacion&action=calcular&id_familia=10` Retorna individualmente el puntaje sumado de una sola célula familiar (útil para auditoria y reportes independientes).

## Próximos pasos
Iniciaremos la validación cruzada y procederemos a encapsular las lógicas de Entregas (Fase 5) manejado de bloqueos de despachos repetidos y reducciones transaccionales en tiempo real.

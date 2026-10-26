# MyTeacher — Auditoría inicial UX/UI y plan de implementación

Fecha: 2026-10-26. Estado: diagnóstico técnico parcial y primera base visual, sin validación de producción.

## Arquitectura observada
- PHP con vistas en `src/views/`, páginas y assets en `public/`.
- La plantilla carga varias capas CSS (`app`, `redesign`, `pro`, `modern`, `academy`, más CSS específicos), lo que aumenta la complejidad de la cascada.
- Las pantallas existentes incluyen inicio, dashboard, unidades, libros, cuaderno de gramática, diccionario, glosario y estadísticas.
- El dashboard tiene seguimiento de ejercicios e intentos; no se deben inventar indicadores que no estén persistidos.

## Riesgos identificados
1. Reglas CSS repetidas y numerosas declaraciones `!important`: pueden causar regresiones visuales.
2. Portada con exceso de llamados de registro duplicados y jerarquía inconsistente.
3. Navegación lateral con múltiples tratamientos visuales acumulados.
4. Teoría/práctica tienen distintas reglas de scroll y sticky entre unidad y gramática.
5. Activos externos de fotografía requieren revisión de accesibilidad, rendimiento y disponibilidad.

## Dirección visual
- Color primario azul profundo, superficies claras, tipografía legible, iconografía SVG coherente.
- Separación deliberada entre portada comercial y zona de estudio.
- Clases prefijadas `mtp-` y tokens reutilizables para reducir colisiones.
- Componentes con foco visible, contraste suficiente y movimiento reducido.

## Trabajo implementado en esta rama
- `public/assets/premium-foundation.css`: tokens y primera composición hero aislada.
- `src/views/home.php`: hero opt-in, copy y CTA más enfocados.
- `src/views/layout_top.php`: inclusión final de la hoja nueva para cascada estable.

## Próximas etapas antes de fusionar
1. Inspeccionar el sitio en desktop, tablet y teléfono con sesión anónima y autenticada.
2. Confirmar carga de fotografías y funcionamiento de botones.
3. Refactorizar componentes de navegación, eliminando reglas duplicadas.
4. Unificar lectores teóricos, práctica, listening y diccionario.
5. Ejecutar tests existentes (smoke, study_book y accesibilidad) y revisar regresiones.
6. Recién después de validar, aprobar merge y desplegar.

No se cambiaron autenticación, datos, API, progreso ni suscripciones.

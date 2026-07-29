<p align="center">
  <img src="pix/tresipunt_logo.svg" alt="Tresipunt" height="60">
  &nbsp;&nbsp;&nbsp;
  <img src="pix/icon.svg" alt="" height="60">
</p>

<h1 align="center">Importación de cursos desde Google Classroom</h1>

<p align="center">
  <img src="https://img.shields.io/badge/version-2.0.0-informational" alt="Version">
  <a href="https://moodle.org"><img src="https://img.shields.io/badge/Moodle-4.5%20%7C%205.1-orange?logo=moodle" alt="Moodle"></a>
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/License-GPL--3.0-green" alt="License">
  <a href="https://tresipunt.com"><img src="https://img.shields.io/badge/made%20by-Tresipunt-F84015" alt="Made by Tresipunt"></a>
</p>

<p align="center"><b>Convierte tus clases de Google Classroom en cursos de Moodle, con su contenido dentro de Moodle.</b></p>

<p align="center"><a href="README.md">🇬🇧 English</a> · <b>🇪🇸 Español</b></p>

Plugin local que importa una clase de Google Classroom como un curso de Moodle:
secciones, materiales y trabajos, anuncios, la carpeta del profesor y el
calendario. Cada curso se importa en su propia tarea en segundo plano, con una
página de progreso y un panel de historial. No modifica el núcleo de
Moodle ni el tema.

---

## ✨ Qué hace

- **Importa la clase como curso** — nombre, subtítulo y aula en el resumen; los
  *temas* de Classroom se convierten en secciones, en su orden original.
- **Trae el contenido a Moodle** — los ficheros de Drive se descargan al curso
  (o se enlazan, según ajuste); los enlaces y vídeos quedan como recursos URL.
- **Mapea trabajos y materiales a actividades** — pregunta de opción múltiple →
  Consulta; pregunta corta → Retroalimentación; tarea con rúbrica → Tarea con su
  rúbrica; formulario de Google → etiqueta incrustada.
- **Anuncios → foro de novedades**; **carpeta del profesor →** carpeta oculta
  cuyos ficheros viven en Moodle; **calendario →** eventos del curso.
- **Publicación programada** — respeta la fecha de publicación de Classroom como
  restricción de acceso «disponible desde».
- **Seguimiento** — página de progreso en vivo (con reintento y descarte) y
  panel de historial con filtros y detalle.

## 🔄 Qué se importa (mapeo)

| Google Classroom | Moodle |
|---|---|
| Clase | Curso (nombre, subtítulo y aula en el resumen) |
| Temas | Secciones del curso (en su orden) |
| Fichero de Drive adjunto | Fichero traído al curso (o enlace, según ajuste) |
| Enlace / vídeo de YouTube | Recurso URL |
| Pregunta de opción múltiple | Consulta |
| Pregunta corta (sin calificación) | Retroalimentación |
| Tarea (con rúbrica) | Tarea (con su rúbrica) |
| Formulario de Google | Etiqueta con el formulario incrustado |
| Anuncios | Debates en el foro de novedades |
| Carpeta del profesor | Carpeta oculta con los ficheros dentro de Moodle |
| Calendario | Eventos del curso |
| Publicación programada | Restricción de acceso «disponible desde» |

**Aún no se importa** (previsto para próximas versiones):

- Entregas, notas y comentarios de los estudiantes.
- Matrículas: otros profesores y los estudiantes de la clase (hoy solo se
  matricula al usuario que importa como profesor).
- Las **preguntas** de los formularios de Google (hoy el formulario se incrusta;
  no se convierte en cuestionario/retroalimentación con sus preguntas).
- Enlaces de Google Meet como actividad de videoconferencia (hoy quedan como
  enlace).

> La imagen de portada de la clase y la descarga de vídeos de YouTube no son
> posibles por límites de la API de Google.

## ⚙️ Cómo funciona

- Conecta una cuenta de Google por OAuth 2.0 y lee la clase con las APIs de
  Classroom, Drive, Calendar y Forms.
- Cada curso se importa en una **tarea programada** independiente; el estado y
  las trazas se guardan para la página de progreso y el panel.
- El contenido se **trae a Moodle** (no se enlaza a Google) siempre que es
  posible, para no depender de la cuenta de Google tras la importación.
- No toca cursos existentes salvo el que crea; se desactiva quitando las
  capacidades o desinstalando el plugin.

## 📋 Requisitos

| Requisito | Versión |
|---|---|
| Moodle | 4.5 LTS o 5.1 |
| PHP | 8.1 o superior |
| Otros plugins | No requiere |
| Cron de Moodle | Necesario — las importaciones corren como tareas en segundo plano |
| Servicios Google | Proyecto de Google Cloud con las APIs de Classroom, Drive, Calendar y Forms, y un cliente OAuth 2.0 de tipo *aplicación web* |

> Cada sitio usa **su propio cliente OAuth**; los pasos, más abajo en *Ajustes*.

## 🚀 Instalación

1. Copiar el código en `<MOODLE_ROOT>/local/tresipuntimportgc/`.
2. Completar la instalación desde **Administración del sitio › Notificaciones**
   (o por CLI: `php admin/cli/upgrade.php --non-interactive`).
3. Purgar las cachés (**Administración del sitio › Desarrollo › Purgar cachés**
   o `php admin/cli/purge_caches.php`).

## 🔧 Ajustes

### Dar acceso a Google (una vez por sitio)

El plugin necesita **un cliente OAuth del propio sitio**: el scope de Drive es
*restricted*, y una aplicación compartida entre organizaciones obligaría a pasar
una evaluación de seguridad anual de pago. Con un proyecto propio y audiencia
*Interna* no hace falta ninguna verificación de Google.

1. En [console.cloud.google.com](https://console.cloud.google.com), con una cuenta
   de **Google Workspace**, crear un **proyecto**.
2. **APIs y servicios › Biblioteca**: habilitar **Classroom**, **Drive**,
   **Calendar** y **Forms**. Solo esas cuatro.
3. **Google Auth Platform**: completar nombre y correos de la aplicación y poner la
   **audiencia en «Interna»**.
4. Pestaña **Clients › Create client › Aplicación web**. En *URIs de redirección
   autorizados*, pegar **exactamente** la que muestra el bloque de conexión de los
   ajustes del plugin (Google exige coincidencia literal: `https`, sin barra final).
   ⚠️ El **secreto solo se muestra al crear el cliente**.
5. Pegar **ID de cliente** y **secreto** en los ajustes y pulsar **Probar conexión**
   antes de dejar importar a nadie.

| Si algo falla | Suele ser |
|---|---|
| `redirect_uri_mismatch` | La URI no coincide con la del bloque de conexión: `http` en vez de `https`, barra final de más, o un host distinto del `wwwroot` |
| «Esta app no está verificada» | La audiencia quedó *Externa* sin publicar: pasarla a **Interna**, o añadir la cuenta como usuario de prueba |
| «API … is disabled» | Falta habilitar una de las cuatro APIs (paso 2); el mensaje dice cuál |
| Faltan clases en el listado | La cuenta conectada no es profesora en ellas, o son de otro Workspace. Las archivadas sí salen |
| Las importaciones no arrancan | El cron de Moodle no está en marcha |

### Opciones

En **Administración del sitio › Extensiones › Extensiones locales › Importación
de cursos desde Google Classroom**:

| Ajuste | Efecto |
|---|---|
| **ID de cliente / Secreto** | Credenciales del cliente OAuth de Google. La URI de redirección a registrar en Google se muestra, copiable, en el bloque de conexión. |
| **Probar conexión** | Lanza el flujo OAuth real para validar las credenciales. |
| **Ficheros de Drive** | Qué hacer con los ficheros adjuntos: traerlos a Moodle, enlazarlos o no importarlos. |
| **Calendario / Formularios / Contenido individual** | Comportamiento por defecto de esos elementos en la importación. |
| **Retención y tamaño de página del panel** | Días que se conserva el historial y filas por página. |

Para importar: **Administración del sitio › Cursos › Importar cursos desde
Google Classroom** (también bajo la categoría del plugin en *Extensiones*).
Requiere la capacidad `local/tresipuntimportgc:import` (por defecto, gestores y
creadores de cursos).

## 🗑️ Desinstalación

Al desinstalar se eliminan las tablas del plugin (historial de importaciones y
trazas) y sus ajustes. Los cursos ya importados **permanecen** como cualquier
otro curso de Moodle.

## 🛠️ Desarrollo (opcional)

```bash
# Tests unitarios
vendor/bin/phpunit --testsuite local_tresipuntimportgc_testsuite

# Tests de aceptación
vendor/bin/behat --tags @local_tresipuntimportgc
```

Tras tocar `amd/src/*.js`, recompilar con `grunt amd`. La librería
`google/apiclient` v2 va vendorizada en `.extlib/`.

## 📄 Licencia

[GNU GPL v3 or later](https://www.gnu.org/copyleft/gpl.html) — 2026 [Tresipunt](https://tresipunt.com) (contacte@tresipunt.com)

---

<p align="center">
  <a href="https://tresipunt.com"><img src="pix/tresipunt_logo.svg" alt="Tresipunt" width="160"></a>
</p>

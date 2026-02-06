=== Comunas de Chile para WooCommerce ===
Contributors: AndresReyesDev
Tags: woocommerce, chile, comunas, shipping, checkout
Requires at least: 5.0
Tested up to: 6.7
Stable Tag: 2026.01.25
Requires PHP: 7.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Agrega las Comunas de Chile a WooCommerce para mejorar la experiencia de envío.

== Description ==

Este plugin permite:
- Modificar el nombre del campo "State" por "Comuna".
- Añadir la lista completa de las 346 comunas de Chile para optimizar la experiencia de compra.
- **Compatible con el Checkout Clásico y el nuevo Checkout de Bloques de WooCommerce.**
- Compatibilidad declarada con WooCommerce HPOS y bloques de carrito/checkout.

== Features ==

1. Soporte oficial para las 346 comunas de Chile, basado en datos oficiales del Gobierno de Chile.
2. **Compatible con el nuevo Checkout de Bloques de WooCommerce** (introducido en WC 8.3+).
3. Compatible con el checkout clásico de WooCommerce.
4. Notificación en el área de administración con opciones adicionales para activar servicios de envío.
5. Elimina campos irrelevantes como el código postal en Chile.
6. Declaración de compatibilidad con HPOS (High-Performance Order Storage).

== Installation ==

Para instalar este plugin:

1. Suba el archivo `woocommerce-comunas.php` a la carpeta `/wp-content/plugins/`.
2. Active el plugin a través del menú "Plugins" en WordPress.
3. ¡Listo! La lista de comunas estará disponible en el checkout de WooCommerce.

== Frequently Asked Questions ==

= ¿Es esta la lista oficial de comunas de Chile? =
Sí, esta lista se basa en los datos disponibles en el Portal de Datos del Gobierno de Chile, ajustada según el Decreto Exento Nº 817 del Ministerio del Interior.

= ¿Qué ocurre si hay actualizaciones en las comunas? =
Puedes actualizar el archivo `communes.php` si hay cambios oficiales en la lista de comunas.

= ¿Cómo desactivo la notificación del plugin en el área de administración? =
La notificación se puede desactivar haciendo clic en el botón "Cerrar" o vía AJAX. También puedes eliminar la opción correspondiente en la base de datos (`chilecourier_notice_dismissed`).

== Changelog ==

= 2026.01.25 =
- **NUEVO: Compatibilidad completa con el Checkout de Bloques de WooCommerce.**
- Integración con WooCommerce Blocks API para modificar campos dinámicamente.
- El código postal se oculta automáticamente para Chile en ambos tipos de checkout.
- Corregido typo en nombre de archivo principal (woocoomerce → woocommerce).
- Agregada verificación de que WooCommerce esté activo antes de inicializar.
- Mejorada seguridad: sanitización de nonce y verificación de capacidades de usuario.
- Removida dependencia de archivo JS externo inexistente.
- Agregado Text Domain propio para internacionalización.
- Actualizada compatibilidad: WordPress 6.7, WooCommerce 9.5.

= 2024.12.08 =
- Nueva versión con mejoras visuales en las notificaciones administrativas.
- Compatibilidad confirmada con WooCommerce 8.7.
- Notificación en el área de administración mejorada.

= 2020.08.05 =
- Cambios en la estructura de almacenamiento de comunas, siguiendo el estándar de WooCommerce.

= 2020.07.26 =
- Mejora en la compatibilidad y código.

= 0.1 =
- Versión inicial.

== Upgrade Notice ==

= 2026.01.25 =
Actualización de seguridad importante. Incluye correcciones de sanitización, verificación de WooCommerce y compatibilidad con las versiones más recientes.

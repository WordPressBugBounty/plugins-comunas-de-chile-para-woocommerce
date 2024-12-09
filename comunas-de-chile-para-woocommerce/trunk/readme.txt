=== Comunas de Chile para WooCommerce ===
Contributors: AndresReyesDev
Tags: woocommerce, chile, comunas, shipping, checkout, webpay
Requires at least: 3.0
Tested up to: 6.7
Stable tag: 2024.12.08
Requires PHP: 7.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Agrega las Comunas de Chile a WooCommerce para mejorar la experiencia de envío.

== Description ==

Este plugin permite:
- Modificar el nombre del campo "State" por "Comuna".
- Añadir la lista completa de las 346 comunas de Chile para optimizar la experiencia de compra.
- Compatibilidad declarada con WooCommerce 8.7 y bloques de carrito y checkout.

== Features ==

1. Soporte oficial para las 346 comunas de Chile, basado en datos oficiales del Gobierno de Chile.
2. Notificación en el área de administración con opciones adicionales para activar servicios de envío.
3. Elimina campos irrelevantes como el código postal en Chile.

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

= 2024.12.08 =
Actualiza a esta versión para asegurar compatibilidad con las versiones más recientes de WordPress y WooCommerce.

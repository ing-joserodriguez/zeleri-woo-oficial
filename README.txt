=== Zeleri Pay ===
Tags: Pagos Online, Pasarela de Pago, Pago con tarjetas, Pago con transferencias
Requires at least: 5.6
Tested up to: 6.7.1
Stable tag: 1.0.2
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Contributors: wpzeleri

Permite el pago de productos y/o servicios, con tarjetas de crédito, débito, prepago y transferencias electrónicas.



== Descripción ==
La integración de Zeleri para [WooCommerce/WordPress] permite a tu tienda aceptar pagos con tarjetas de crédito, débito y prepago, así como transferencias bancarias de manera fácil y segura.



== Preguntas Frecuentes ==
1. ¿Qué es Zeleri?
Zeleri es una solución integral de pagos en línea que ofrece links de pago, múltiples opciones de transacción y monitoreo en tiempo real. Garantizamos rapidez y seguridad tanto para comercios como para clientes, facilitando la adopción de métodos de pago sin fricciones.

2. ¿Funciona en cualquier país?
Zeleri está disponible sólo para procesar pagos en Chile.

3. ¿Hay algún requisito mínimo de sistema o servidor?
No, Zeleri no requiere configuraciones especiales de sistema o servidor. Solo necesitas una conexión a internet para comenzar a realizar transacciones.

4. ¿Este plugin permite diferentes métodos de pago?
Sí, la integración de Zeleri para [WooCommerce/WordPress] permite a tu tienda aceptar pagos con tarjetas de crédito, débito, prepago y transferencias bancarias.

5. ¿El plugin cumple con las normativas de seguridad y privacidad de datos?
Zeleri cumple con las normativas PCI-DSS y cuenta con certificaciones de la CMF y DRI, garantizando altos estándares de seguridad y protección de datos.

6. ¿Dónde puedo obtener soporte si tengo problemas con el plugin?
Puedes visitar nuestro foro de soporte en [Zeleri](https://zeleri.com/ "Una plataforma, múltiples soluciones") o ponerte en contacto con nuestro equipo a través de teléfono o WhatsApp al [+569 4420 9837].

7. ¿El plugin es gratuito o tiene un costo de suscripción?
La integración de Zeleri para [WooCommerce/WordPress] es completamente gratuita.

8. ¿Qué debo hacer si el plugin no funciona correctamente después de una actualización?
Si experimentas problemas tras una actualización, puedes contactarnos vía llamada o WhatsApp al [+569 4420 9837]. También puedes consultar nuestro foro de ayuda en [Zeleri](https://zeleri.com/ "Una plataforma, múltiples soluciones") para obtener asistencia adicional.

9. ¿Cómo se instala el plugin en mi tienda e-commerce?
Después de activar Zeleri en tu [WooCommerce/WordPress], solo necesitas conectar tu cuenta de Zeleri utilizando tu Zeleri Key y API Key.

10. ¿Qué es el Zeleri Key?
El Zeleri Key es una clave única de tu comercio, necesaria para realizar transacciones. Puedes solicitarla contactando al soporte de Zeleri por WhatsApp o llamada al [+569 4420 9837].

11. ¿Qué es el API Key?
El API Key complementa al Zeleri Key y es fundamental para integrar tu tienda con Zeleri. Al igual que el Zeleri Key, puedes obtenerlo contactando a nuestro equipo de soporte al [+569 4420 9837].



== Uso de APIs Externas ==
Este plugin se conecta a la API externa Zeleri Checkout API para generar órdenes de pago que los usuarios pueden pagar a través de un checkout, donde pueden seleccionar el método de pago (tarjeta de crédito/débito o transferencia bancaria).
Aca puedes verificar los [terminos y condiciones](https://zeleri.com/terminos-y-condiciones/ "Una plataforma, múltiples soluciones") de nuestra API.

* API Utilizada:
Zeleri API

* URL de la API:
https://api.service.zeleri.com/v1

= Funcionalidad =
El plugin utiliza la Zeleri Checkout API para:
* Generar órdenes de pago a partir de pedidos creados en WordPress.
* Redirigir al usuario a un checkout donde puede seleccionar su método de pago preferido (tarjeta de crédito/débito o transferencia bancaria).
* Gestionar el estado del pago, actualizando la información de la orden en WordPress cuando la transacción ha sido completada o fallida.

= Datos Enviados =
El plugin envía los siguientes datos a la Zeleri Checkout API para generar la orden de pago:
* ID de la orden: Un identificador único de la orden generada en WordPress.
* Total de la orden: El monto total que debe pagarse.
* Moneda: La moneda en la que se realizará el pago.
* Datos del cliente: Nombre, correo electrónico, y otros datos relevantes del cliente.
* Método de pago seleccionado: Información sobre si el cliente eligió pagar con tarjeta o transferencia bancaria.

= Datos Recibidos =
La API devuelve la siguiente información, que es utilizada por el plugin para completar la transacción:
* Estado de la transacción: Si el pago fue exitoso, fallido o está en proceso.
* ID de transacción: El identificador único de la transacción procesada por Zeleri.
* Detalles del pago: Información sobre el método de pago utilizado y la cantidad pagada.

= Seguridad =
El plugin implementa varias medidas de seguridad para garantizar la integridad y confidencialidad de los datos:
* Todas las solicitudes a la Zeleri Checkout API se realizan a través de HTTPS.
* El plugin requiere que el administrador del sitio configure una clave de API proporcionada por Zeleri, que se utiliza para autenticar las solicitudes.
* Los datos sensibles, como la información del cliente, no se almacenan localmente en WordPress, sino que se transmiten de manera segura a través de la API.

= Requerimientos =
* Para que el plugin funcione correctamente, es necesario obtener una clave y secret de API Zeleri, solicitada previamente a nuestros agentes de customer success y configurarla en la sección de ajustes del plugin. 
* La clave y secret de API asegura que las solicitudes entre WordPress y Zeleri estén autenticadas y autorizadas.



== Changelog ==
= 1.0.2 =
* Pagos mediante tarjeta de credito y debito.
* Pagos mediante transferencias bancarias.
* Listado de transaccciones realizadas con Zeleri Pay

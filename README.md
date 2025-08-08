
# Amplifica WooCommerce Integration

Plataforma de gestión y reportería para tiendas WooCommerce, desarrollada en Laravel.

## Características principales

- Autenticación de usuarios (registro/login)
- Conexión de múltiples tiendas WooCommerce por usuario
- Visualización de productos (nombre, SKU, precio, imagen)
- Visualización de pedidos recientes (cliente, fecha, productos, estado)
- Exportación de productos y pedidos a Excel
- Dashboard con métricas agregadas (ventas por mes, productos más vendidos)
- Filtros avanzados en pedidos
- Logs de errores
- Sincronización manual de datos (comando artisan)
- Panel para administrar múltiples tiendas

## Instalación

1. Clona el repositorio:
	```bash
	git clone https://github.com/apizarro1204/test-amplifica.git
	cd test-amplifica
	```
2. Instala dependencias:
	```bash
	composer install
	npm install && npm run build
	```
3. Copia el archivo de entorno y configura tus variables:
	```bash
	cp .env.example .env
	php artisan key:generate
	```
4. Configura la base de datos en `.env` y ejecuta migraciones:
	```bash
	php artisan migrate
	```
5. (Opcional) Ejecuta los tests:
	```bash
	php artisan test
	```
6. Inicia el servidor:
	```bash
	php artisan serve
	```

## Uso

1. Regístrate o inicia sesión.
2. En el menú de usuario podrás agregar una nueva tienda WooCommerce en el link "Agregar nueva tienda".
	- Debes ingresar la URL de la tienda y las API Keys (Consumer Key y Consumer Secret) generadas desde WooCommerce.
	- Por el momento, **solo se soporta WooCommerce**.
3. Una vez conectada la tienda, podrás:
	- Ver productos y pedidos en tiempo real (API en vivo)
	- Exportar productos y pedidos a Excel
	- Visualizar métricas y reportería en el dashboard
	- Filtrar pedidos por fecha, cliente o estado

## Sincronización y almacenamiento local

El sistema permite sincronizar productos y pedidos localmente usando el comando:

```bash
php artisan sync:woocommerce
```

Esto descarga los datos de todas las tiendas conectadas y los almacena en la base de datos local. Por defecto, la visualización en la app es en vivo desde la API.

## Pruebas

Para ejecutar los tests de integración y funcionalidad:

```bash
php artisan test
```

## Estructura y arquitectura

- **Integración WooCommerce:** Toda la lógica de integración está en el backend, en servicios y controladores dedicados.
- **Seguridad:** Las credenciales de API se almacenan en la base de datos y nunca en texto plano en el código.
- **Frontend:** Se utiliza Blade y Tailwind para una experiencia moderna y responsiva.
- **Logs:** Los errores de integración se registran en la base de datos para su revisión.

## Notas adicionales

- El sistema está preparado para soportar múltiples tiendas por usuario.
- El soporte para Shopify u otras plataformas puede agregarse fácilmente extendiendo la arquitectura de servicios.

---

## Uso de la IA
- **Copilot**
    - Se utilizó Copilot en VisualStudio Code para generar funciones genéricas. También se utilizó para la instalación de Laravel y otras dependencias,además de crear Test unitarios para las funcionalidades. Se utilizó GPT-4.1 para solicitudes sencillas y Claude Sonnet 4 para solicitudes complejas o resolución de problemas y errores que no logré visualizar rápidamente en el código. Al finalizar el proyecto utilicé nuevamente Claude 4 para refactorizar algún código y revisar si no existen funciones repetitivas o mal integradas. 

Desarrollado para la prueba técnica de Amplifica.

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

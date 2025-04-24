# Tienda de Materiales PVC

Sistema de comercio electrónico especializado en la venta de materiales de PVC y productos para almacenamiento de agua.

## Características Principales

- Catálogo de productos organizado por categorías
- Carrito de compras
- Panel de administración
- Sistema de pedidos
- Gestión de productos

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL/MariaDB
- Servidor web (Apache/Nginx)

## Instalación

1. Clone el repositorio o descargue los archivos en su servidor web
2. Importe el archivo `database.sql` en su servidor MySQL/MariaDB
3. Configure los parámetros de conexión a la base de datos en `includes/config.php`
4. Asegúrese de que el directorio `assets` tenga permisos de escritura

## Estructura del Proyecto

```
├── admin/              # Panel de administración
│   ├── dashboard.php   # Panel principal
│   ├── orders.php      # Gestión de pedidos
│   ├── products.php    # Gestión de productos
│   └── product-form.php# Formulario de productos
├── assets/            # Recursos estáticos
│   └── css/           # Hojas de estilo
├── includes/          # Archivos de configuración
│   ├── config.php     # Configuración general
│   ├── database.php   # Conexión a base de datos
│   └── functions.php  # Funciones auxiliares
├── index.php         # Página principal
├── products.php      # Catálogo de productos
├── cart.php         # Carrito de compras
└── checkout.php     # Proceso de compra
```

## Configuración

1. Acceda al archivo `includes/config.php` y configure las siguientes variables:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'usuario');
   define('DB_PASS', 'contraseña');
   define('DB_NAME', 'nombre_base_datos');
   ```

2. Configure el nombre de su tienda:
   ```php
   define('SITE_NAME', 'Nombre de su Tienda');
   ```

## Panel de Administración

- Acceda a `/admin` para gestionar productos y pedidos
- Las credenciales por defecto son:
  - Usuario: admin
  - Contraseña: admin123

## Funcionalidades

### Gestión de Productos
- Agregar, editar y eliminar productos
- Asignar categorías
- Gestionar precios e inventario
- Subir imágenes de productos

### Carrito de Compras
- Agregar/eliminar productos
- Actualizar cantidades
- Calcular totales

### Proceso de Compra
- Formulario de datos del cliente
- Resumen de pedido
- Confirmación de compra

## Seguridad

- Validación de datos de entrada
- Protección contra SQL Injection
- Sanitización de salida HTML
- Sesiones seguras

## Soporte

Para reportar problemas o solicitar ayuda, por favor cree un issue en el repositorio del proyecto.

## Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo LICENSE para más detalles.
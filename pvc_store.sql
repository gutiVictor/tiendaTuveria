-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-04-2025 a las 02:06:22
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pvc_store`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '2025-04-20 20:01:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image_url`, `category`, `created_at`, `updated_at`) VALUES
(1, 'pvc', 'material rigido', 120.00, 20, 'https://www.rotolia.com/blog/wp-content/uploads/2022/01/caracteristicas-pvc.jpg', 'tuveria', '2025-04-20 20:03:46', '2025-04-20 23:27:02'),
(2, 'Tanque PVC 500L', 'Tanque vertical para agua, color azul, resistente a UV', 120.50, 15, 'https://colempaques.com/cdn/shop/files/Tanque_de_Agua_Conico_500_L.jpg?v=1741423749', 'Almacenamiento', '2025-04-20 20:32:38', '2025-04-20 21:56:21'),
(3, 'Tanque PVC 1000L', 'Tanque industrial para almacenamiento prolongado de agua', 210.00, 8, 'https://i0.wp.com/pinturasyyesos.com/wp-content/uploads/2021/01/Tanque_Acuaviva_1000.jpg?fit=600%2C600&ssl=1', 'Almacenamiento', '2025-04-20 20:32:38', '2025-04-20 22:05:27'),
(4, 'Tanque PVC 2000L', 'Gran capacidad para uso agrícola, incluye tapa de seguridad', 350.75, 5, 'https://sanpioawsecommerceprod.siesaecommerce.com/backend/admin/backend/web/archivosDelCliente/items/images/PVC-TANQUES-TANQUE-ACUAVIVA-NEGRO-2000L-SIN-TAPA-37820240717095602.jpg', 'Almacenamiento', '2025-04-20 20:32:38', '2025-04-20 22:54:34'),
(5, 'Tubo PVC 1/2\" x 6m', 'Tubo para conducción de agua potable, presión 10 bar', 8.99, 120, 'https://promartecuador.vtexassets.com/arquivos/ids/190780/tubo-pvc-sch-40-1-2-6m-rival-32275_01.jpg?v=638380086044770000', 'Tubería', '2025-04-20 20:32:38', '2025-04-20 22:55:38'),
(6, 'Tubo PVC 1\" x 6m', 'Ideal para sistemas de riego, color blanco', 12.50, 80, 'https://elpalustre.com.co/image/cache/catalog/Celta/Tubo%20presion%201-760x1000.jpg', 'Tubería', '2025-04-20 20:32:38', '2025-04-20 22:56:56'),
(7, 'Tubo PVC 2\" x 6m', 'Uso industrial, resistente a químicos', 18.25, 50, 'https://gt.epaenlinea.com/media/catalog/product/cache/3f8a07f91ed96197ac7613a4e8859f2d/d/8/d883006f-9377-4e5b-9de2-65a0ceab15d9.jpg', 'Tubería', '2025-04-20 20:32:38', '2025-04-20 22:58:36'),
(8, 'Codo PVC 90° 1/2\"', 'Codo para cambio de dirección en instalaciones', 1.20, 200, 'https://admintienda.coval.com.co/backend/admin/backend/web/archivosDelCliente/items/images/20220803165039-TUBERIA-Y-ACCESORIOS-PRESION-AGUA-FRIA-Codo-90-Pvc-Presion-de-12-Gerfor-176202208031650393228.jpg', 'Accesorios', '2025-04-20 20:32:38', '2025-04-20 22:13:55'),
(9, 'Tee PVC 1\"', 'Conexión en T para derivaciones', 2.10, 150, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR1DuMs0etesY2cBqkpJfgHiz5UT5tKH2n-UQ&s', 'Accesorios', '2025-04-20 20:32:38', '2025-04-20 22:59:23'),
(10, 'Unión PVC 2\"', 'Unión rápida para reparaciones', 3.50, 100, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS8qZ0ms27rb_dPft2WxsJEfNWhEqfzkawqPw&s', 'Accesorios', '2025-04-20 20:32:38', '2025-04-20 23:00:27'),
(11, 'Válvula esférica PVC 1/2\"', 'Válvula de cierre manual, rosca hembra', 5.75, 60, 'https://admintienda.coval.com.co/backend/admin/backend/web/archivosDelCliente/items/images/VALVULAS-DE-BOLA-PVC-Y-CPVC-Valvula-Bola-Soldada-Pvc-de-12-Pcp-48720210408071702.jpg', 'Válvulas', '2025-04-20 20:32:38', '2025-04-20 23:31:45'),
(12, 'Válvula check PVC 1\"', 'Evita retroceso de flujo en tuberías', 7.30, 40, 'https://http2.mlstatic.com/D_NQ_NP_696569-MCO78829213507_082024-O.webp', 'Válvulas', '2025-04-20 20:32:38', '2025-04-20 23:01:58'),
(13, 'Válvula compuerta PVC 2\"', 'Para control total de flujo, uso industrial', 12.90, 25, 'https://http2.mlstatic.com/D_NQ_NP_624923-MCO78828070607_082024-O.webp', 'Válvulas', '2025-04-20 20:32:38', '2025-04-20 23:02:42'),
(14, 'Bomba sumergible 1HP', 'Bomba para pozos profundos, 220V', 150.00, 12, 'https://autosolar.co/images/bombas-agua-sumergibles/bomba-sumergible-pearl-4pwp35g-10c36p3-1hp-220v-trifasico.jpg', 'Bombas', '2025-04-20 20:32:38', '2025-04-20 23:03:53'),
(15, 'Bomba superficial 0.5HP', 'Ideal para cisternas y tanques elevados', 89.99, 18, 'https://www.evans.com.co/wp-content/uploads/2016/09/Bombas_de_Superficie_EVANS_BP4_7ME050_1L_35.jpg', 'Bombas', '2025-04-20 20:32:38', '2025-04-20 23:04:28'),
(16, 'Bomba solar para riego', 'Energía solar, incluye panel de 100W', 240.50, 7, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ97FwTIOSZa3XCOb4tFlThaqG9PUyCXFC59Q&s', 'Bombas', '2025-04-20 20:32:38', '2025-04-20 23:12:06'),
(17, 'Filtro sedimentador PVC', 'Retiene partículas de hasta 50 micras', 25.00, 30, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSMYUQWK2RNikPHbnzYVDAazxo7zsi31RGWng&s', 'Filtros', '2025-04-20 20:32:38', '2025-04-20 23:12:56'),
(18, 'Purificador UV 10L/min', 'Elimina bacterias con luz ultravioleta', 180.25, 10, 'https://http2.mlstatic.com/D_NQ_NP_933480-MCO72944853481_112023-O.webp', 'Filtros', '2025-04-20 20:32:38', '2025-04-20 23:13:44'),
(19, 'Filtro de carbón activado', 'Mejora sabor y olor del agua', 45.60, 20, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT1XVlC--NwkfkMlTcglGKkxbPZmm34A5jNug&s', 'Filtros', '2025-04-20 20:32:38', '2025-04-20 23:15:32'),
(20, 'Kit riego por goteo', 'Incluye tubería, goteros y temporizador', 55.00, 25, 'https://http2.mlstatic.com/D_NQ_NP_975600-MCO53304749425_012023-O.webp', 'Kits', '2025-04-20 20:32:38', '2025-04-20 23:17:06'),
(21, 'Kit instalación tanque 1000L', 'Tuberías, válvulas y accesorios completos', 95.75, 10, 'https://disensa.com.ec/wp-content/uploads/2020/11/91001557_C.jpg', 'Kits', '2025-04-20 20:32:38', '2025-04-20 23:21:00'),
(22, 'Kit emergencia agua potable', 'Filtro + bomba manual + 10L almacenamiento', 75.30, 15, 'https://www.lovibond.com/ix_pim_assets/Wasseranalytik/Instruments/Test_Kits/Potable_Water_Safety_Kits/MB2/56K681250_WaterSafetyKit_Basic_Case_Content_mb2.jpg', 'Kits', '2025-04-20 20:32:38', '2025-04-20 23:22:23'),
(23, 'Geomembrana PVC 1mm', 'Para impermeabilización de estanques', 3.50, 200, 'https://icofesa.com/sistema/fotos/1producto23648.jpg', 'Especializados', '2025-04-20 20:32:38', '2025-04-20 23:23:10'),
(24, 'Sifón PVC para drenaje', 'Sistema anti-olor para desagües', 12.25, 45, 'https://elarenal.com.gt/cdn/shop/products/Sifon-Pvc-Drenaje-1-1-2-Terminal_636619af-8cef-4879-8dbe-5545cf068b44.jpg?v=1645475886', 'Especializados', '2025-04-20 20:32:38', '2025-04-20 23:25:22'),
(25, 'Caja de registro PVC', 'Para acceso a tuberías subterráneas', 18.90, 30, 'https://tornitec.com/wp-content/uploads/2023/05/CAJA-DE-REGISTRO-PVC.jpg', 'Especializados', '2025-04-20 20:32:38', '2025-04-20 23:26:05'),
(26, 'Junta tórica PVC 1/2\"', 'Sellado para conexiones hidráulicas', 0.75, 500, 'https://m.media-amazon.com/images/I/41q4IhhFrAL._AC_SL1001_.jpg', 'Repuestos', '2025-04-20 20:32:38', '2025-04-20 23:28:34'),
(27, 'Arandela PVC 1\"', 'Refuerzo para uniones de tuberías', 0.50, 400, 'https://www.deplano.com.ar/12592-large_default/arandela-pvc-plana-p-sopapa-%C3%B8-1-1-2-20.jpg', 'Repuestos', '2025-04-20 20:32:38', '2025-04-20 23:29:26'),
(28, 'Tapa PVC 2\"', 'Cierre sanitario para tuberías', 1.20, 300, 'https://tectul.com/rails/active_storage/blobs/proxy/eyJfcmFpbHMiOnsiZGF0YSI6NjcwODgsInB1ciI6ImJsb2JfaWQifX0=--5b5d251b6880038d88c8e3b29644c1344fc06f6f/tapa-pvc-presion-soldar?locale=es', 'Repuestos', '2025-04-20 20:32:38', '2025-04-20 23:30:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

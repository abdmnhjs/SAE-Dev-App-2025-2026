

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



CREATE TABLE `constructor` (
                               `id` int(11) NOT NULL,
                               `name` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `control_unit` (
                                `name` varchar(255) DEFAULT NULL,
                                `serial` varchar(100) NOT NULL,
                                `id_constructor` int(11) DEFAULT NULL,
                                `model` varchar(100) DEFAULT NULL,
                                `type` varchar(50) DEFAULT NULL,
                                `cpu` varchar(100) DEFAULT NULL,
                                `ram_mb` int(11) DEFAULT NULL,
                                `disk_gb` int(11) DEFAULT NULL,
                                `id_operating_sytem` int(11) DEFAULT NULL,
                                `domain` varchar(100) DEFAULT NULL,
                                `location` varchar(100) DEFAULT NULL,
                                `building` varchar(100) DEFAULT NULL,
                                `room` varchar(50) DEFAULT NULL,
                                `macaddr` varchar(17) DEFAULT NULL,
                                `purchase_date` date DEFAULT NULL,
                                `warranty_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `operating_system_list` (
                                         `id` int(11) NOT NULL,
                                         `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `screen` (
                          `serial` varchar(100) NOT NULL,
                          `manufacturer` varchar(100) DEFAULT NULL,
                          `model` varchar(100) DEFAULT NULL,
                          `size_inch` decimal(4,1) DEFAULT NULL,
                          `resolution` varchar(20) DEFAULT NULL,
                          `connector` varchar(50) DEFAULT NULL,
                          `attached_to` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `users` (
                         `id` int(11) NOT NULL,
                         `name` varchar(255) NOT NULL,
                         `mdp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `users` (`id`, `name`, `mdp`) VALUES
                                              (1, 'sysadmin', 'sysadmin'),
                                              (2, 'adminweb', 'adminweb'),
                                              (3, 'tech', 'tech');


ALTER TABLE `constructor`
    ADD PRIMARY KEY (`id`);


ALTER TABLE `control_unit`
    ADD PRIMARY KEY (`serial`),
  ADD KEY `id_operating_sytem` (`id_operating_sytem`),
  ADD KEY `id_constructor` (`id_constructor`);


ALTER TABLE `operating_system_list`
    ADD PRIMARY KEY (`id`);


ALTER TABLE `screen`
    ADD PRIMARY KEY (`serial`);


ALTER TABLE `users`
    ADD PRIMARY KEY (`id`);


ALTER TABLE `constructor`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `operating_system_list`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `users`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `control_unit`
    ADD CONSTRAINT `control_unit_ibfk_1` FOREIGN KEY (`id_operating_sytem`) REFERENCES `operating_system_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `control_unit_ibfk_2` FOREIGN KEY (`id_constructor`) REFERENCES `constructor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
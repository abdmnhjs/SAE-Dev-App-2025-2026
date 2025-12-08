CREATE DATABASE IF NOT EXISTS infra;
USE infra;

-- Table des utilisateurs
CREATE OR REPLACE TABLE users (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL
);

-- Table des systèmes d'exploitation
CREATE OR REPLACE TABLE os_list (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) NOT NULL UNIQUE
);

-- Table des fabricants
CREATE OR REPLACE TABLE manufacturer_list (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) NOT NULL UNIQUE
);

-- Table des unités de contrôle (ordinateurs/serveurs)
CREATE OR REPLACE TABLE control_unit (
    serial VARCHAR(100),
    name VARCHAR(255) NOT NULL PRIMARY KEY,
    id_manufacturer INTEGER,
    model VARCHAR(100),
    type VARCHAR(50),
    cpu VARCHAR(100),
    ram_mb INTEGER,
    disk_gb INTEGER,
    id_os INTEGER,
    domain VARCHAR(100),
    location VARCHAR(100),
    building VARCHAR(100),
    room VARCHAR(50),
    macaddr VARCHAR(17),
    purchase_date DATE,
    warranty_end DATE,

    -- Clés étrangères
    CONSTRAINT fk_control_unit_manufacturer
        FOREIGN KEY (id_manufacturer)
        REFERENCES manufacturer_list(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_control_unit_os
        FOREIGN KEY (id_os)
        REFERENCES os_list(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Table des écrans
CREATE OR REPLACE TABLE screen (
    serial VARCHAR(100) PRIMARY KEY,
    id_manufacturer INTEGER,
    model VARCHAR(100),
    size_inch DECIMAL(4,1),
    resolution VARCHAR(20),
    connector VARCHAR(50),
    attached_to VARCHAR(100),

    -- Clés étrangères
    CONSTRAINT fk_screen_manufacturer
        FOREIGN KEY (id_manufacturer)
        REFERENCES manufacturer_list(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_screen_attached
        FOREIGN KEY (attached_to)
        REFERENCES control_unit(name)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Insertion des données de test
INSERT INTO users (name, mdp)
VALUES
    ('sysadmin', 'sysadmin'),
    ('adminweb', 'adminweb'),
    ('tech', 'tech');

-- Exemples de données pour les tables de référence
INSERT INTO os_list (name) VALUES
                               ('Windows 11'),
                               ('Windows 10'),
                               ('Ubuntu 22.04'),
                               ('Debian 12'),
                               ('macOS Sonoma');

INSERT INTO manufacturer_list (name) VALUES
                                         ('Dell'),
                                         ('HP'),
                                         ('Lenovo'),
                                         ('Apple'),
                                         ('Samsung'),
                                         ('LG');

-- Exemples de données pour control_unit
INSERT INTO control_unit (serial, name, id_manufacturer, model, type, cpu, ram_mb, disk_gb, id_os, domain, location, building, room, macaddr, purchase_date, warranty_end)
VALUES
    ('SN001', 'PC-Admin-01', 1, 'OptiPlex 7090', 'Desktop', 'Intel i7-11700', 16384, 512, 1, 'CORP.LOCAL', 'Headquarters', 'Building A', 'A-101', '00:1A:2B:3C:4D:5E', '2023-01-15', '2026-01-15'),
    ('SN002', 'SRV-DB-01', 1, 'PowerEdge R740', 'Server', 'Intel Xeon Gold 6230', 65536, 2048, 4, 'CORP.LOCAL', 'Datacenter', 'Building B', 'DC-01', '00:1A:2B:3C:4D:5F', '2022-06-10', '2025-06-10');

-- Exemples de données pour screen
INSERT INTO screen (serial, id_manufacturer, model, size_inch, resolution, connector, attached_to)
VALUES
    ('MON001', 1, 'P2422H', 24.0, '1920x1080', 'DisplayPort', 'PC-Admin-01'),
    ('MON002', 5, 'S27A600', 27.0, '2560x1440', 'HDMI', 'SRV-DB-01');
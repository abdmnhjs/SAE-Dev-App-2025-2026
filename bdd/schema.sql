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

CREATE OR REPLACE TABLE logs (
    id INTEGER AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    duration_seconds INT UNSIGNED NOT NULL,
    log_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
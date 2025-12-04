CREATE DATABASE infra;
USE infra;

CREATE OR REPLACE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    mdp VARCHAR(255) NOT NULL
);

CREATE OR REPLACE TABLE control_unit (
    name VARCHAR(255),
    serial VARCHAR(100),
    manufacturer VARCHAR(100),
    model VARCHAR(100),
    type VARCHAR(50),
    cpu VARCHAR(100),
    ram_mb INTEGER,
    disk_gb INTEGER,
    os VARCHAR(100),
    domain VARCHAR(100),
    location VARCHAR(100),
    building VARCHAR(100),
    room VARCHAR(50),
    macaddr VARCHAR(17),
    purchase_date DATE,
    warranty_end DATE
);

CREATE OR REPLACE TABLE screen (
    serial VARCHAR(100),
    manufacturer VARCHAR(100),
    model VARCHAR(100),
    size_inch DECIMAL(4,1),
    resolution VARCHAR(20),
    connector VARCHAR(50),
    attached_to VARCHAR(100)
);

INSERT INTO users (name, mdp)
VALUES
    ('sysadmin', 'sysadmin'),
    ('adminweb', 'adminweb'),
    ('tech', 'tech');


-- Create database
CREATE DATABASE db_sepeda;

-- Use the newly created database
USE db_sepeda;

-- Create the 'sepeda' table
CREATE TABLE sepeda (
    id_sepeda INT AUTO_INCREMENT PRIMARY KEY,
    nama_sepeda VARCHAR(255) NOT NULL,
    merk VARCHAR(255) NOT NULL,
    tahun_produksi INT NOT NULL,
    id_tipe INT NOT NULL,
    id_kategori INT NOT NULL,
    stok INT NOT NULL,
    harga DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL
);

-- Create the 'tipe' table
CREATE TABLE tipe (
    id_tipe INT AUTO_INCREMENT PRIMARY KEY,
    nama_tipe VARCHAR(255) NOT NULL
);

-- Create the 'kategori' table
CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(255) NOT NULL
);

-- Insert sample data into 'tipe' table
INSERT INTO tipe (nama_tipe) VALUES
('Mountain Bike'),
('Road Bike'),
('Hybrid Bike'),
('Electric Bike');

-- Insert sample data into 'kategori' table
INSERT INTO kategori (nama_kategori) VALUES
('Sport'),
('Touring'),
('City'),
('Kids');

-- Insert sample data into 'sepeda' table
INSERT INTO sepeda (nama_sepeda, merk, tahun_produksi, id_tipe, id_kategori, stok, harga) VALUES
('Trek Marlin 7', 'Trek', 2023, 1, 1, 10, 800.00),
('Giant Defy Advanced 2', 'Giant', 2022, 2, 2, 5, 2000.00),
('Cannondale Quick 4', 'Cannondale', 2021, 3, 3, 7, 900.00),
('Specialized Turbo Vado 4.0', 'Specialized', 2023, 4, 3, 3, 3500.00);

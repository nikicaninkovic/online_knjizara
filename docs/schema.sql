-- docs/schema.sql
-- Šema za online knjižaru (UTF8MB4, InnoDB, FK, normalizacija 3NF)
-- Kreiraj bazu (po potrebi promeni ime)
CREATE DATABASE IF NOT EXISTS online_knjizara
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE online_knjizara;

-- 1) Korisnici
CREATE TABLE IF NOT EXISTS users (
                                     id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                     email        VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(120) NOT NULL,
    role         ENUM('customer','admin') NOT NULL DEFAULT 'customer',
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

-- 2) Autori (jednostavno; knjiga ima jednog autora za sada; lako se širi na N:M ako zatreba)
CREATE TABLE IF NOT EXISTS authors (
                                       id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                       name       VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

-- 3) Žanrovi (dimenziona tabela)
CREATE TABLE IF NOT EXISTS genres (
                                      id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                      name       VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;

-- 4) Knjige (osnovni katalog)
CREATE TABLE IF NOT EXISTS books (
                                     id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                     title         VARCHAR(255) NOT NULL,
    author_id     INT UNSIGNED NOT NULL,
    price         DECIMAL(10,2) NOT NULL,
    stock_qty     INT UNSIGNED NOT NULL DEFAULT 0,
    isbn13        VARCHAR(13) NULL UNIQUE,
    published_at  DATE NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_books_author
    FOREIGN KEY (author_id) REFERENCES authors(id)
                                ON UPDATE CASCADE
                                ON DELETE RESTRICT
    ) ENGINE=InnoDB;

CREATE INDEX idx_books_title ON books(title);
CREATE INDEX idx_books_price ON books(price);

-- 5) N:M veza: knjiga ↔ žanr
CREATE TABLE IF NOT EXISTS book_genre (
                                          book_id  INT UNSIGNED NOT NULL,
                                          genre_id INT UNSIGNED NOT NULL,
                                          PRIMARY KEY (book_id, genre_id),
    CONSTRAINT fk_bg_book
    FOREIGN KEY (book_id) REFERENCES books(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    CONSTRAINT fk_bg_genre
    FOREIGN KEY (genre_id) REFERENCES genres(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    ) ENGINE=InnoDB;

CREATE INDEX idx_bg_genre ON book_genre(genre_id);

-- 6) Narudžbine (zaglavlje)
CREATE TABLE IF NOT EXISTS orders (
                                      id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                      user_id       INT UNSIGNED NOT NULL,
                                      status        ENUM('new','paid','shipped','cancelled','refunded') NOT NULL DEFAULT 'new',
    total_amount  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user
    FOREIGN KEY (user_id) REFERENCES users(id)
                                ON UPDATE CASCADE
                                ON DELETE RESTRICT
    ) ENGINE=InnoDB;

CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);

-- 7) Stavke narudžbine (detalji)
CREATE TABLE IF NOT EXISTS order_items (
                                           id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                           order_id     BIGINT UNSIGNED NOT NULL,
                                           book_id      INT UNSIGNED NOT NULL,
                                           unit_price   DECIMAL(10,2) NOT NULL,   -- kopija cene u trenutku kupovine (istorijska tačnost)
    quantity     INT UNSIGNED NOT NULL,
    line_total   DECIMAL(12,2) GENERATED ALWAYS AS (unit_price * quantity) STORED,
    CONSTRAINT fk_oi_order
    FOREIGN KEY (order_id) REFERENCES orders(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    CONSTRAINT fk_oi_book
    FOREIGN KEY (book_id) REFERENCES books(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
    ) ENGINE=InnoDB;

CREATE INDEX idx_oi_order ON order_items(order_id);
CREATE INDEX idx_oi_book ON order_items(book_id);

-- (opciono) minimalni seed za test
INSERT INTO authors (name) VALUES
                               ('Ivo Andrić'), ('Meša Selimović'), ('Antoine de Saint-Exupéry');

INSERT INTO genres (name) VALUES
                              ('Klasika'), ('Roman'), ('Strani');

INSERT INTO books (title, author_id, price, stock_qty, isbn13, published_at) VALUES
                                                                                 ('Na Drini ćuprija', 1, 12.90, 10, '9788652112028', '1945-01-01'),
                                                                                 ('Derviš i smrt',    2, 14.00,  8, '9788673463412', '1966-01-01'),
                                                                                 ('Mali princ',       3,  9.90, 15, '9789533135634', '1943-04-06');

INSERT INTO book_genre (book_id, genre_id) VALUES
                                               (1,1),(1,2),
                                               (2,1),(2,2),
                                               (3,2),(3,3);

-- Primer test korisnika (lozinka je samo placeholder; u app-u koristi password_hash)
INSERT INTO users (email, password_hash, display_name, role)
VALUES ('customer@example.com', '$2y$10$examplehashOVDE', 'Kupac Demo', 'customer');

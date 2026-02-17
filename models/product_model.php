<?php
// models/product_model.php
declare(strict_types=1);

require_once __DIR__ . '/../db/database.php';

/**
 * Convert any standard date input to YYYY-MM-DD (Project 10-1).
 *
 * Examples accepted:
 *  - 2026-02-17
 *  - 02/17/2026
 *  - Feb 17 2026
 *  - 17 Feb 2026
 *
 * @throws InvalidArgumentException if invalid
 */
function normalize_release_date(string $input): string
{
    $input = trim($input);

    // strtotime handles many formats; false means invalid
    $ts = strtotime($input);
    if ($ts === false) {
        throw new InvalidArgumentException('Please enter a valid release date.');
    }

    return date('Y-m-d', $ts);
}

/**
 * Format YYYY-MM-DD to m-d-yyyy with no leading zeros (Project 10-1).
 */
function format_release_date_for_list(?string $dbDate): string
{
    if (!$dbDate) {
        return '';
    }

    $ts = strtotime($dbDate);
    if ($ts === false) {
        return '';
    }

    return date('n-j-Y', $ts); // no leading zeros
}

/**
 * Get all products ordered by name (better for dropdowns).
 *
 * @return array<int, array<string,mixed>>
 */
function get_products(): array
{
    global $db;

    $query = '
        SELECT productCode, name, version, releaseDate
        FROM products
        ORDER BY name
    ';

    $statement = $db->query($query);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get a single product by code.
 */
function get_product(string $code): ?array
{
    global $db;

    $query = '
        SELECT productCode, name, version, releaseDate
        FROM products
        WHERE productCode = :code
        LIMIT 1
    ';
    $statement = $db->prepare($query);
    $statement->execute([':code' => $code]);
    $product = $statement->fetch(PDO::FETCH_ASSOC);

    return $product ?: null;
}

/**
 * Add a new product.
 *
 * @param string $release_date YYYY-MM-DD
 */
function add_product(string $code, string $name, string $version, string $release_date): void
{
    global $db;

    $query = '
        INSERT INTO products (productCode, name, version, releaseDate)
        VALUES (:code, :name, :version, :release_date)
    ';
    $statement = $db->prepare($query);
    $statement->execute([
        ':code'         => $code,
        ':name'         => $name,
        ':version'      => $version,
        ':release_date' => $release_date,
    ]);
}

/**
 * Update an existing product.
 *
 * @param string $release_date YYYY-MM-DD
 */
function update_product(string $code, string $name, string $version, string $release_date): void
{
    global $db;

    $query = '
        UPDATE products
        SET name = :name,
            version = :version,
            releaseDate = :release_date
        WHERE productCode = :code
    ';
    $statement = $db->prepare($query);
    $statement->execute([
        ':code'         => $code,
        ':name'         => $name,
        ':version'      => $version,
        ':release_date' => $release_date,
    ]);
}

/**
 * Delete a product by code.
 */
function delete_product(string $code): void
{
    global $db;

    $query = 'DELETE FROM products WHERE productCode = :code';
    $statement = $db->prepare($query);
    $statement->execute([':code' => $code]);
}
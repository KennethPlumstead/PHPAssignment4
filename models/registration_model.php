<?php
// models/registration_model.php
declare(strict_types=1);

require_once __DIR__ . '/../db/database.php';

/**
 * Register a product for a customer.
 */
function register_product(int $customerID, string $productCode): void
{
    global $db;

    $sql = '
        INSERT INTO registrations (customerID, productCode, registrationDate)
        VALUES (:customerID, :productCode, NOW())
    ';

    $statement = $db->prepare($sql);

    try {
        $statement->execute([
            ':customerID'  => $customerID,
            ':productCode' => $productCode,
        ]);
    } catch (PDOException $e) {
        // If UNIQUE(customerID, productCode) exists, this handles double-register attempts safely
        if ((int)($e->errorInfo[1] ?? 0) === 1062) {
            // Duplicate entry
            return;
        }
        throw $e;
    }
}

/**
 * Check if a product is already registered for a customer.
 */
function is_product_registered(int $customerID, string $productCode): bool
{
    global $db;

    $sql = '
        SELECT COUNT(*)
        FROM registrations
        WHERE customerID = :customerID
          AND productCode = :productCode
    ';

    $statement = $db->prepare($sql);
    $statement->execute([
        ':customerID'  => $customerID,
        ':productCode' => $productCode,
    ]);

    return (int)$statement->fetchColumn() > 0;
}

/**
 * Get products registered to a customer (for Create Incident drop-down).
 *
 * @return array<int, array<string,mixed>>
 */
function get_registered_products_for_customer(int $customerID): array
{
    global $db;

    $sql = '
        SELECT p.productCode, p.name
        FROM registrations r
        JOIN products p ON p.productCode = r.productCode
        WHERE r.customerID = :customerID
        ORDER BY p.name
    ';

    $statement = $db->prepare($sql);
    $statement->execute([':customerID' => $customerID]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
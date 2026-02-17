<?php
// models/incident_model.php
declare(strict_types=1);

require_once __DIR__ . '/../db/database.php';

/**
 * Create a new incident (techID starts NULL, dateClosed starts NULL).
 */
function add_incident(int $customerID, string $productCode, string $title, string $description): void
{
    global $db;

    $sql = '
        INSERT INTO incidents (customerID, productCode, techID, dateOpened, dateClosed, title, description)
        VALUES (:customerID, :productCode, NULL, NOW(), NULL, :title, :description)
    ';

    $statement = $db->prepare($sql);
    $statement->execute([
        ':customerID'  => $customerID,
        ':productCode' => $productCode,
        ':title'       => $title,
        ':description' => $description,
    ]);
}
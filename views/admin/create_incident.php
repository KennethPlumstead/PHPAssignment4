<?php
// views/admin/create_incident.php
declare(strict_types=1);

require_once __DIR__ . '/../../models/customer_model.php';
require_once __DIR__ . '/../../models/registration_model.php';
require_once __DIR__ . '/../../models/incident_model.php';

$error = '';
$success = '';

$customerID = (int)($_GET['customerID'] ?? $_POST['customerID'] ?? 0);
if ($customerID <= 0) {
    header('Location: get_customer.php');
    exit;
}

$customer = get_customer($customerID);
if (!$customer) {
    header('Location: get_customer.php');
    exit;
}

$registeredProducts = get_registered_products_for_customer($customerID);

// Keep form values sticky
$productCode  = trim($_POST['productCode'] ?? '');
$title        = trim($_POST['title'] ?? '');
$description  = trim($_POST['description'] ?? '');

$action = $_POST['action'] ?? $_GET['action'] ?? 'view';

switch ($action) {
    case 'create_incident':
        if ($productCode === '' || $title === '' || $description === '') {
            $error = 'Please select a product and enter a title and description.';
            break;
        }

        // Ensure product is actually in the registered list
        $allowed = false;
        foreach ($registeredProducts as $p) {
            if (($p['productCode'] ?? '') === $productCode) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            $error = 'That product is not registered for this customer.';
            break;
        }

        try {
            add_incident($customerID, $productCode, $title, $description);
            $success = 'Incident was added to the database.';

            // Clear form after success
            $productCode = '';
            $title = '';
            $description = '';
        } catch (PDOException $e) {
            $error = 'Unable to create incident. Please try again.';
        }
        break;

    case 'view':
    default:
        // just display the form
        break;
}

include __DIR__ . '/../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Create Incident</h2>
    <a href="get_customer.php" class="btn btn-outline-secondary btn-sm">&larr; Get Customer</a>
</div>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-secondary text-white fw-semibold">
        Customer: <?= htmlspecialchars(($customer['firstName'] ?? '') . ' ' . ($customer['lastName'] ?? '')) ?>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-6">
                <div class="small text-muted">Email</div>
                <div class="fw-semibold"><?= htmlspecialchars($customer['email'] ?? '') ?></div>
            </div>
            <div class="col-md-6">
                <div class="small text-muted">Customer ID</div>
                <div class="fw-semibold"><?= (int)$customer['customerID'] ?></div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-success text-white fw-semibold">
        Incident Details
    </div>
    <div class="card-body">
        <?php if (count($registeredProducts) === 0): ?>
            <div class="alert alert-warning mb-0">
                This customer has no registered products. Register a product first.
            </div>
        <?php else: ?>
            <form method="post" action="create_incident.php" class="row g-3" novalidate>
                <input type="hidden" name="action" value="create_incident">
                <input type="hidden" name="customerID" value="<?= (int)$customerID ?>">

                <div class="col-md-6">
                    <label for="productCode" class="form-label">Product</label>
                    <select class="form-select" id="productCode" name="productCode" required>
                        <option value="">-- Select a product --</option>
                        <?php foreach ($registeredProducts as $p): ?>
                            <option value="<?= htmlspecialchars($p['productCode']) ?>"
                                <?= $productCode === ($p['productCode'] ?? '') ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['productCode']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label for="title" class="form-label">Title</label>
                    <input
                        type="text"
                        class="form-control"
                        id="title"
                        name="title"
                        value="<?= htmlspecialchars($title) ?>"
                        required
                    >
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea
                        class="form-control"
                        id="description"
                        name="description"
                        rows="4"
                        required
                    ><?= htmlspecialchars($description) ?></textarea>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-success">Create Incident</button>
                    <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary ms-2">Back to Home</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
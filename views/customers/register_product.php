<?php
// views/customers/register_product.php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../models/product_model.php';
require_once __DIR__ . '/../../models/registration_model.php';

$error = '';
$success = '';

if (!isset($_SESSION['customer'])) {
    header('Location: customer_login.php');
    exit;
}

$customer = $_SESSION['customer'];
$customerID = (int)($customer['customerID'] ?? 0);

if ($customerID <= 0) {
    header('Location: customer_login.php?action=logout');
    exit;
}

$products = get_products(); // includes all products (Project 6-4 spec)

$action = $_POST['action'] ?? $_GET['action'] ?? 'view';
$selectedProductCode = trim($_POST['productCode'] ?? '');

switch ($action) {
    case 'register_product':
        if ($selectedProductCode === '') {
            $error = 'Please select a product.';
            break;
        }

        try {
            if (is_product_registered($customerID, $selectedProductCode)) {
                $error = 'That product is already registered for this customer.';
                break;
            }

            register_product($customerID, $selectedProductCode);
            $success = 'Product was registered successfully. Product code: ' . $selectedProductCode;

            // Clear selection after success
            $selectedProductCode = '';
        } catch (PDOException $e) {
            $error = 'Unable to register product. Please try again.';
        }
        break;

    case 'view':
    default:
        break;
}

include __DIR__ . '/../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Register Product</h2>
    <div class="d-flex gap-2">
        <a href="customer_login.php?action=logout" class="btn btn-outline-danger btn-sm">
            Logout
        </a>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary btn-sm">
            Back to Home
        </a>
    </div>
</div>

<div class="alert alert-info">
    Logged in as <strong><?= htmlspecialchars($customer['email'] ?? '') ?></strong>
</div>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($success !== ''): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-success text-white fw-semibold">
        Register a Product
    </div>

    <div class="card-body">
        <form action="register_product.php" method="post" class="row g-3" novalidate>
            <input type="hidden" name="action" value="register_product">

            <div class="col-md-6">
                <label for="productCode" class="form-label">Product</label>
                <select class="form-select" id="productCode" name="productCode" required>
                    <option value="">-- Select a product --</option>
                    <?php foreach ($products as $p): ?>
                        <?php $code = (string)($p['productCode'] ?? ''); ?>
                        <option value="<?= htmlspecialchars($code) ?>"
                            <?= $selectedProductCode === $code ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['name'] ?? '') ?> (<?= htmlspecialchars($code) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-success">
                    Register Product
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
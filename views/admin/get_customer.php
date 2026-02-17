<?php
// views/admin/get_customer.php
declare(strict_types=1);

require_once __DIR__ . '/../../models/customer_model.php';

$error = '';
$email = trim($_POST['email'] ?? '');

if (($_POST['action'] ?? '') === 'get_customer') {
    if ($email === '') {
        $error = 'Please enter the customer’s email address.';
    } else {
        $customer = get_customer_by_email($email);

        if (!$customer) {
            $error = 'No customer found with that email address.';
        } else {
            header('Location: create_incident.php?customerID=' . urlencode((string)$customer['customerID']));
            exit;
        }
    }
}

include __DIR__ . '/../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Get Customer</h2>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary btn-sm">&larr; Back to Home</a>
</div>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-semibold">
        Customer Lookup
    </div>
    <div class="card-body">
        <form method="post" action="get_customer.php" class="row g-3" novalidate>
            <input type="hidden" name="action" value="get_customer">

            <div class="col-md-6">
                <label for="email" class="form-label">Customer Email</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($email) ?>"
                    required
                >
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Get Customer</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
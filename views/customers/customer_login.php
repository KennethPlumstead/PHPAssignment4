<?php
// views/customers/customer_login.php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../../models/customer_model.php';

$action = $_POST['action'] ?? $_GET['action'] ?? 'view';

$error = '';
$email = trim($_POST['email'] ?? '');

// If already logged in, skip login page (Project 12-1)
if (isset($_SESSION['customer']) && $action === 'view') {
    header('Location: register_product.php');
    exit;
}

switch ($action) {
    case 'login':
        if ($email === '') {
            $error = 'Please enter your email address.';
            break;
        }

        $customer = get_customer_by_email($email);
        if (!$customer) {
            $error = 'No customer found with that email address.';
            break;
        }

        // Store customer data in session (Project 12-1)
        $_SESSION['customer'] = $customer;

        header('Location: register_product.php');
        exit;

    case 'logout':
        // End session + remove cookie
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        // Start a fresh anonymous session id (good practice)
        session_start();
        session_regenerate_id(true);

        header('Location: customer_login.php');
        exit;

    case 'view':
    default:
        break;
}

include __DIR__ . '/../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Customer Login</h2>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary btn-sm">&larr; Back to Home</a>
</div>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white fw-semibold">
        Login
    </div>
    <div class="card-body">
        <form action="customer_login.php" method="post" class="row g-3" novalidate>
            <input type="hidden" name="action" value="login">

            <div class="col-md-6">
                <label for="email" class="form-label">Email Address</label>
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
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../footer.php'; ?>
<?php
// views/admin/product_manager.php

require_once __DIR__ . '/../../models/product_model.php';

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';
$error = '';
$edit_product = null;

// Handle actions
switch ($action) {
    case 'add_product':
        $code               = trim($_POST['productCode'] ?? '');
        $name               = trim($_POST['name'] ?? '');
        $version            = trim($_POST['version'] ?? '');
        $release_date_input = trim($_POST['releaseDate'] ?? '');

        if ($code === '' || $name === '' || $version === '' || $release_date_input === '') {
            $error = 'All fields are required to add a product.';
            break;
        }

        try {
            // Project 10-1: accept any date format, store as YYYY-MM-DD
            $release_date = normalize_release_date($release_date_input);

            add_product($code, $name, $version, $release_date);
            header('Location: product_manager.php');
            exit;
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = 'Error adding product: ' . $e->getMessage();
        }
        break;

    case 'show_edit':
        $code = $_GET['productCode'] ?? '';
        if ($code !== '') {
            $edit_product = get_product($code);
            if (!$edit_product) {
                $error = 'Product not found.';
            }
        } else {
            $error = 'No product code provided for edit.';
        }
        break;

    case 'update_product':
        $code               = trim($_POST['productCode'] ?? '');
        $name               = trim($_POST['name'] ?? '');
        $version            = trim($_POST['version'] ?? '');
        $release_date_input = trim($_POST['releaseDate'] ?? '');

        if ($code === '' || $name === '' || $version === '' || $release_date_input === '') {
            $error = 'All fields are required to update a product.';
            break;
        }

        try {
            // Project 10-1: accept any date format, store as YYYY-MM-DD
            $release_date = normalize_release_date($release_date_input);

            update_product($code, $name, $version, $release_date);
            header('Location: product_manager.php');
            exit;
        } catch (InvalidArgumentException $e) {
            $error = $e->getMessage();
        } catch (PDOException $e) {
            $error = 'Error updating product: ' . $e->getMessage();
        }
        break;

    case 'delete_product':
        $code = $_POST['productCode'] ?? '';
        if ($code !== '') {
            try {
                delete_product($code);
                header('Location: product_manager.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Error deleting product: ' . $e->getMessage();
            }
        } else {
            $error = 'No product code provided for delete.';
        }
        break;

    case 'list':
    default:
        break;
}

// Always get the current product list
$products = get_products();

// ✅ Correct includes (header/footer live in /views)
include __DIR__ . '/../header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Product Manager</h2>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary btn-sm">
        &larr; Back to Home
    </a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white fw-semibold">
        Product List
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Version</th>
                    <th>Release Date</th>
                    <th class="text-center">Edit</th>
                    <th class="text-center">Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($products): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['productCode']) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['version']) ?></td>
                        <td><?= htmlspecialchars(format_release_date_for_list($product['releaseDate'] ?? '')) ?></td>
                        <td class="text-center">
                            <a
                                class="btn btn-sm btn-outline-primary"
                                href="product_manager.php?action=show_edit&productCode=<?= urlencode($product['productCode']) ?>">
                                Edit
                            </a>
                        </td>
                        <td class="text-center">
                            <form action="product_manager.php" method="post" class="d-inline">
                                <input type="hidden" name="action" value="delete_product">
                                <input type="hidden" name="productCode"
                                       value="<?= htmlspecialchars($product['productCode']) ?>">
                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Delete this product?');">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center py-3">
                        No products found.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white fw-semibold">
        <?= $edit_product ? 'Edit Product' : 'Add Product' ?>
    </div>

    <div class="card-body">
        <form action="product_manager.php" method="post" class="row g-3">
            <input type="hidden" name="action"
                   value="<?= $edit_product ? 'update_product' : 'add_product' ?>">

            <?php if ($edit_product): ?>
                <div class="col-md-3">
                    <label class="form-label">Product Code</label>
                    <input type="text" class="form-control"
                           name="productCode"
                           value="<?= htmlspecialchars($edit_product['productCode']) ?>"
                           readonly>
                </div>
            <?php else: ?>
                <div class="col-md-3">
                    <label class="form-label">Product Code</label>
                    <input type="text" class="form-control" name="productCode" required>
                </div>
            <?php endif; ?>

            <div class="col-md-5">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="name"
                       value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Version</label>
                <input type="text" class="form-control" name="version"
                       value="<?= htmlspecialchars($edit_product['version'] ?? '') ?>" required>
            </div>

            <div class="col-md-2">
                <label class="form-label">Release Date</label>
                <!-- Project 10-1: allow ANY standard date format -->
                <input
                    type="text"
                    class="form-control"
                    name="releaseDate"
                    placeholder="e.g., 2/17/2026 or Feb 17 2026"
                    value="<?= htmlspecialchars($edit_product ? format_release_date_for_list($edit_product['releaseDate'] ?? '') : '') ?>"
                    required
                >
                <div class="form-text">
                    Any common date format is accepted.
                </div>
            </div>

            <div class="col-12 d-flex justify-content-between">
                <button type="submit" class="btn btn-success">
                    <?= $edit_product ? 'Update Product' : 'Add Product' ?>
                </button>

                <?php if ($edit_product): ?>
                    <a href="product_manager.php" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php
include __DIR__ . '/../footer.php';
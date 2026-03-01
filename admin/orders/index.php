<?php
/**
 * Admin Orders Management — Brand Luxury Perfumes
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$pdo = getDB();
$pageTitle = 'إدارة الطلبات';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!validateCSRF())
        die('طلب غير صالح');
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->execute(['id' => (int) $_POST['delete_id']]);
    header('Location: ' . url('admin/') . 'orders/?deleted=1');
    exit;
}

// Stats
$stats = [
    'all' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'new' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='new'")->fetchColumn(),
    'contacted' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='contacted'")->fetchColumn(),
    'completed' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status='completed'")->fetchColumn(),
];

// Filters
$statusFilter = $_GET['status'] ?? 'all';
$query = "SELECT o.*, p.name_ar AS p_name FROM orders o LEFT JOIN products p ON p.id = o.product_id";
$params = [];

if (in_array($statusFilter, ['new', 'contacted', 'completed', 'cancelled'])) {
    $query .= " WHERE o.status = :status";
    $params['status'] = $statusFilter;
}

$query .= " ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$csrfToken = generateCSRF();
include __DIR__ . '/../includes/admin_header.php';
?>

<div class="admin-page-title">
    <span>الطلبات</span>
</div>

<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success" data-auto-dismiss="4000"> تم حذف الطلب بنجاح</div>
<?php endif; ?>

<!-- Filters -->
<div class="admin-filters">
    <a href="?status=all"
        class="admin-btn <?= $statusFilter === 'all' ? 'admin-btn-primary' : 'admin-btn-outline' ?>">الكل (
        <?= $stats['all'] ?>)
    </a>
    <a href="?status=new"
        class="admin-btn <?= $statusFilter === 'new' ? 'admin-btn-primary' : 'admin-btn-outline' ?>">جديد (
        <?= $stats['new'] ?>)
    </a>
    <a href="?status=contacted"
        class="admin-btn <?= $statusFilter === 'contacted' ? 'admin-btn-primary' : 'admin-btn-outline' ?>">تم التواصل (
        <?= $stats['contacted'] ?>)
    </a>
    <a href="?status=completed"
        class="admin-btn <?= $statusFilter === 'completed' ? 'admin-btn-primary' : 'admin-btn-outline' ?>">مكتمل (
        <?= $stats['completed'] ?>)
    </a>
</div>

<div class="admin-card">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>رقم</th>
                    <th>العميل</th>
                    <th>المنتج</th>
                    <th>السعر</th>
                    <th>ملاحظات</th>
                    <th>تحديث الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr id="order-row-<?= $order['id'] ?>">
                        <td style="color:var(--admin-gold);font-weight:700;">#
                            <?= $order['id'] ?>
                        </td>
                        <td>
                            <strong style="display:block;">
                                <?= e($order['customer_name'] ?: 'غير متوفر') ?>
                            </strong>
                            <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $order['customer_phone'])) ?>"
                                target="_blank"
                                style="color:var(--admin-muted);font-size:0.8rem;text-decoration:none;display:inline-flex;align-items:center;gap:3px;margin-top:2px;">

                                <?= e($order['customer_phone'] ?: 'غير متوفر') ?>
                            </a>
                        </td>
                        <td>
                            <strong style="color:var(--admin-text);">
                                <?= e($order['p_name'] ?? $order['product_name']) ?>
                            </strong>
                            <?php if ($order['product_id']): ?>
                                <br><a href="<?= url() ?>product.php?id=<?= $order['product_id'] ?>" target="_blank"
                                    class="admin-link" style="font-size:0.75rem;">عرض المنتج</a>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight:700;">
                            <?= number_format($order['product_price'], 0) ?> ر.س
                        </td>
                        <td style="max-width:200px;font-size:0.8rem;white-space:normal;color:var(--admin-muted);">
                            <?= e($order['notes'] ?: '—') ?>
                        </td>
                        <td>
                            <select class="admin-select order-status-select" data-id="<?= $order['id'] ?>"
                                style="padding:0.4rem;font-size:0.8rem;min-width:110px;">
                                <option value="new" <?= $order['status'] === 'new' ? 'selected' : '' ?>>جديد</option>
                                <option value="contacted" <?= $order['status'] === 'contacted' ? 'selected' : '' ?>>تم التواصل
                                </option>
                                <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>مكتمل
                                </option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>ملغي
                                </option>
                            </select>
                            <span class="status-indicator"
                                style="display:none;font-size:0.7rem;color:var(--admin-green);margin-right:5px;"></span>
                        </td>
                        <td style="font-size:0.8rem;color:var(--admin-muted);" dir="ltr">
                            <?= date('M d, Y H:i', strtotime($order['created_at'])) ?>
                        </td>
                        <td>
                            <form method="POST" style="display:inline;"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟')">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <input type="hidden" name="delete_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm"
                                    style="padding:0.3rem 0.6rem;">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--admin-muted);padding:2rem;">لا توجد طلبات
                            لعرضها.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // AJAX Status Update
        document.querySelectorAll('.order-status-select').forEach(select => {
            select.addEventListener('change', async function () {
                const id = this.dataset.id;
                const status = this.value;
                const indicator = this.nextElementSibling;

                this.disabled = true;
                try {
                    const fd = new FormData();
                    fd.append('id', id);
                    fd.append('status', status);
                    fd.append('csrf_token', '<?= $csrfToken ?>');

                    const res = await fetch('<?= url('admin/') ?>orders/update_status.php', {
                        method: 'POST',
                        body: fd
                    });
                    const data = await res.json();

                    if (data.success) {
                        indicator.style.display = 'inline-block';
                        setTimeout(() => indicator.style.display = 'none', 2000);
                    } else {
                        alert(data.error || 'خطأ في التحديث');
                    }
                } catch (err) {
                    alert('حدث خطأ في الاتصال.');
                } finally {
                    this.disabled = false;
                }
            });
        });
    });
</script>

<?php include __DIR__ . '/../includes/admin_footer.php'; ?>
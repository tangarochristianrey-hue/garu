<?php
include 'includes/header.php';
$base_url = $base_url ?? '';

$upload_dir = '../assets/images/certificates/';

// Ensure directory exists
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Months array for dropdown
$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

// Handle Add/Edit Certificate
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id = $_POST['certificate_id'] ?? null;
    $title = $_POST['title'];
    $issued_by = $_POST['issued_by'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $keywords = $_POST['keywords'];
    $description = $_POST['description'];
    
    // File Upload Logic
    $image_name = $_POST['existing_image'] ?? '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_name = uniqid('cert_') . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
    }

    if($_POST['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO certificates (title, issued_by, month, year, image, keywords, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $issued_by, $month, $year, $image_name, $keywords, $description]);
        header("Location: certificates?success=added");
        exit;
    } elseif($_POST['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE certificates SET title=?, issued_by=?, month=?, year=?, image=?, keywords=?, description=? WHERE id=?");
        $stmt->execute([$title, $issued_by, $month, $year, $image_name, $keywords, $description, $id]);
        header("Location: certificates?success=updated");
        exit;
    }
}

// Handle Delete Certificate
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $cert = $pdo->prepare("SELECT image FROM certificates WHERE id = ?");
    $cert->execute([$id]);
    $row = $cert->fetch();
    if($row && $row['image'] && file_exists($upload_dir . $row['image'])) {
        unlink($upload_dir . $row['image']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: certificates?success=deleted");
    exit;
}

$certificates = $pdo->query("SELECT * FROM certificates ORDER BY year DESC, id DESC")->fetchAll();

// If editing
$edit_data = null;
if(isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM certificates WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?= $edit_data ? 'Edit Certificate' : 'Manage Certificates' ?></h1>
            <p class="page-subtitle mb-0">Manage your certifications and achievements.</p>
        </div>
        <?php if($edit_data): ?>
            <a href="certificates" class="btn-outline-custom"><i class="fa-solid fa-arrow-left me-2"></i> Back to List</a>
        <?php else: ?>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-plus me-2"></i> Add Certificate
            </button>
        <?php endif; ?>
    </div>
    
    <?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success bg-dark border-success text-success p-2 mb-4" style="font-size: 0.85rem;">Action completed successfully.</div>
    <?php endif; ?>
    
    <?php if($edit_data): ?>
    
    <!-- EDIT FORM -->
    <div class="card-custom">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="certificate_id" value="<?= $edit_data['id'] ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_data['image'] ?? '') ?>">
            
            <h5 class="text-white fs-6 mb-4 fw-bold">Edit Certificate</h5>
            
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Issued By</label>
                    <input type="text" name="issued_by" class="form-control" value="<?= htmlspecialchars($edit_data['issued_by'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <option value="">Select Month</option>
                        <?php foreach($months as $m): ?>
                            <option value="<?= $m ?>" <?= ($edit_data['month'] == $m) ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <input type="text" name="year" class="form-control" value="<?= htmlspecialchars($edit_data['year'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Certificate Image (Max 2MB)</label>
                    <input type="file" name="image" class="form-control" accept="image/jpeg, image/png, image/webp">
                    <small class="text-muted-custom mt-1 d-block" style="font-size: 0.75rem;">JPG/PNG only, max 2MB.</small>
                    <?php if($edit_data['image']): ?>
                        <div class="mt-2 p-2 border border-secondary rounded d-inline-block" style="background: #111;">
                            <img src="<?= $base_url ?>/assets/images/certificates/<?= htmlspecialchars($edit_data['image']) ?>" class="img-fluid rounded" style="max-height: 100px;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Keywords (comma-separated)</label>
                    <input type="text" name="keywords" class="form-control" value="<?= htmlspecialchars($edit_data['keywords'] ?? '') ?>" placeholder="e.g., .NET Framework, C# Programming">
                    <small class="text-muted-custom mt-1 d-block" style="font-size: 0.75rem;">Separate keywords with commas</small>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                </div>
            </div>
            
            <div class="mt-5 text-start">
                <button type="submit" class="btn-primary-custom px-4 py-2">Update Certificate</button>
            </div>
        </form>
    </div>
    
    <?php else: ?>
    
    <!-- LIST VIEW -->
    <div class="card-custom p-0 overflow-hidden">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="width: 100px;">Image</th>
                    <th>Certificate Details</th>
                    <th style="width: 150px;">Date</th>
                    <th style="width: 120px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($certificates as $cert): ?>
                <tr>
                    <td>
                        <?php if($cert['image']): ?>
                            <img src="<?= $base_url ?>/assets/images/certificates/<?= htmlspecialchars($cert['image']) ?>" class="rounded" style="width: 80px; height: 60px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded d-flex align-items-center justify-content-center bg-dark" style="width: 80px; height: 60px; border: 1px solid #333;">
                                <i class="fa-solid fa-certificate text-muted-custom"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <h6 class="fw-bold mb-1 text-white"><?= htmlspecialchars($cert['title']) ?></h6>
                        <div class="text-muted-custom" style="font-size: 0.8rem;"><?= htmlspecialchars($cert['issued_by']) ?></div>
                    </td>
                    <td class="text-muted-custom" style="font-size: 0.85rem;"><?= htmlspecialchars($cert['month'] . ' ' . $cert['year']) ?></td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="?edit=<?= $cert['id'] ?>" class="btn-outline-custom" style="border-color: #333; padding: 6px 12px;">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="?delete=<?= $cert['id'] ?>" class="btn-outline-custom text-danger delete-btn" style="border-color: #333; padding: 6px 12px;" onclick="return confirm('Are you sure you want to delete this certificate?');">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($certificates) == 0): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted-custom py-4">No certificates added yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="background: #0a0a0a; border: 1px solid #262626; border-radius: 12px;">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold text-white fs-5">Add Certificate</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label">Title *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Issued By</label>
                                <input type="text" name="issued_by" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Month</label>
                                <select name="month" class="form-select">
                                    <option value="">Select Month</option>
                                    <?php foreach($months as $m): ?>
                                        <option value="<?= $m ?>"><?= $m ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Year</label>
                                <input type="text" name="year" class="form-control" value="<?= date('Y') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Certificate Image</label>
                                <input type="file" name="image" class="form-control" accept="image/jpeg, image/png, image/webp" required>
                                <small class="text-muted-custom mt-1 d-block" style="font-size: 0.75rem;">JPG/PNG only, max 2MB</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Keywords (comma-separated)</label>
                                <input type="text" name="keywords" class="form-control" placeholder="e.g., .NET Framework, C# Programming">
                                <small class="text-muted-custom mt-1 d-block" style="font-size: 0.75rem;">Separate keywords with commas</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="mt-4 text-start">
                            <button type="submit" class="btn-primary-custom px-4 py-2">Add Certificate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>

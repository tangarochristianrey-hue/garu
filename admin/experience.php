<?php
include 'includes/header.php';

// Handle Add/Edit Experience
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $title = $_POST['title'];
    $company = $_POST['company'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $type = $_POST['type']; // 'work' or 'education'
    $id = $_POST['id'] ?? null;
    
    if($_POST['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO experience (type, title, company, duration, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$type, $title, $company, $duration, $description]);
        header("Location: experience?success=added");
        exit;
    } elseif($_POST['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE experience SET type=?, title=?, company=?, duration=?, description=? WHERE id=?");
        $stmt->execute([$type, $title, $company, $duration, $description, $id]);
        header("Location: experience?success=updated");
        exit;
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM experience WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: experience?success=deleted");
    exit;
}

$experiences = $pdo->query("SELECT * FROM experience ORDER BY id ASC")->fetchAll();

$edit_data = null;
if(isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM experience WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}
?>

    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-secondary pb-4">
        <div>
            <h1 class="page-title"><?= $edit_data ? 'Edit Timeline Entry' : 'Experience & Education' ?></h1>
            <p class="page-subtitle mb-0">Total control over your professional and academic history.</p>
        </div>
        <?php if($edit_data): ?>
            <a href="experience" class="btn-outline-custom"><i class="fa-solid fa-arrow-left me-2"></i> Back</a>
        <?php else: ?>
            <button class="btn-primary-custom px-4 py-2" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-plus me-2"></i> Add Entry
            </button>
        <?php endif; ?>
    </div>
    
    <?php if($edit_data): ?>
    <div class="card-custom mx-auto" style="max-width: 800px;">
        <form method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Entry Type</label>
                    <select name="type" class="form-control py-3" required>
                        <option value="work" <?= $edit_data['type']=='work'?'selected':'' ?>>Work Experience</option>
                        <option value="education" <?= $edit_data['type']=='education'?'selected':'' ?>>Education</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Main Title</label>
                    <input type="text" name="title" class="form-control py-3" value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Company / School Name</label>
                    <input type="text" name="company" class="form-control py-3" value="<?= htmlspecialchars($edit_data['company'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Duration / Period (e.g. 2022 - Present)</label>
                    <input type="text" name="duration" class="form-control py-3" value="<?= htmlspecialchars($edit_data['duration'] ?? '') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Description (Automatically separated into bullets by periods/dots)</label>
                    <textarea name="description" class="form-control py-3" rows="8" required><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                    <small class="text-muted-custom mt-1 d-block" style="font-size:0.75rem;">Just write your job responsibilities as normal sentences ending with periods. The system will automatically turn every sentence into a separate bullet point!</small>
                </div>
            </div>
            <div class="d-flex gap-3 justify-content-end mt-5">
                <a href="experience" class="btn-outline-custom px-4 py-2">Cancel</a>
                <button type="submit" class="btn-primary-custom px-4 py-2">Save Changes</button>
            </div>
        </form>
    </div>
    <?php else: ?>
    
    <div class="card-custom p-0 overflow-hidden shadow-lg border border-secondary">
        <table class="table mb-0">
            <thead style="background: #111;">
                <tr>
                    <th style="width: 120px;">Type</th>
                    <th>Timeline Entry Details</th>
                    <th style="width: 150px;" class="text-end">Management</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($experiences as $exp): ?>
                <tr>
                    <td>
                        <span style="background: <?= $exp['type']=='work' ? '#222' : '#333' ?>; color: #fff; padding: 6px 14px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; border: 1px solid #444;">
                            <?= ucfirst($exp['type']) ?>
                        </span>
                    </td>
                    <td>
                        <h6 class="fw-bold mb-1 fs-6 text-white"><?= htmlspecialchars($exp['title']) ?></h6>
                        <p class="text-muted-custom mb-0" style="font-size: 0.85rem;"><i class="fa-regular fa-building me-1"></i> <?= htmlspecialchars($exp['company']) ?> <span class="mx-1">•</span> <?= htmlspecialchars($exp['duration']) ?></p>
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="?edit=<?= $exp['id'] ?>" class="btn-outline-custom border-secondary px-3 py-1" title="Edit">
                                <i class="fa-solid fa-pen" style="font-size: 0.8rem;"></i>
                            </a>
                            <a href="?delete=<?= $exp['id'] ?>" class="btn-outline-custom text-danger border-secondary px-3 py-1 delete-btn" title="Delete">
                                <i class="fa-solid fa-trash-can" style="font-size: 0.8rem;"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($experiences) == 0): ?>
                <tr>
                    <td colspan="3" class="text-center text-muted-custom py-5">
                        <i class="fa-solid fa-briefcase fs-1 mb-3"></i>
                        <p>No timeline entries mapped yet.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg" style="background: #0a0a0a; border: 1px solid #333; border-radius: 16px;">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold text-white fs-5">Add Timeline Entry</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Entry Type</label>
                                <select name="type" class="form-control py-3" required>
                                    <option value="work">Work Experience</option>
                                    <option value="education">Education</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Main Title</label>
                                <input type="text" name="title" class="form-control py-3" placeholder="e.g. Senior Developer" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Company / School Name</label>
                                <input type="text" name="company" class="form-control py-3" placeholder="e.g. Google" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duration / Period</label>
                                <input type="text" name="duration" class="form-control py-3" placeholder="e.g. 2023 - Present" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description (Automatically separated into bullets by periods/dots)</label>
                                <textarea name="description" class="form-control py-3" rows="5" placeholder="Assisted customers with orders. Maintained cleanliness of the area." required></textarea>
                                <small class="text-muted-custom mt-1 d-block" style="font-size:0.75rem;">Just write your job responsibilities as normal sentences ending with periods. The system will automatically turn every sentence into a separate bullet point!</small>
                            </div>
                        </div>
                        <div class="d-flex gap-3 justify-content-end mt-5">
                            <button type="button" class="btn-outline-custom px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-primary-custom px-4 py-2">Deploy Entry</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>

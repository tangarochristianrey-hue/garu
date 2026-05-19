<?php
include 'includes/header.php';
$base_url = $base_url ?? '';

$upload_dir = '../assets/images/projects/';

// Ensure directory exists with proper permissions
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Handle Add/Edit Project
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id = $_POST['project_id'] ?? null;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $tags = $_POST['tags'];
    $link = $_POST['link'];
    $client = $_POST['client'];
    $project_date = $_POST['project_date'];
    
    // File Upload Logic
    $main_image_name = $_POST['existing_main_image'] ?? '';
    if(isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $ext = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $main_image_name = uniqid('proj_') . '.' . $ext;
        move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_dir . $main_image_name);
    }
    
    $additional_images_names = [];
    if(!empty($_POST['existing_additional_images'])) {
        $additional_images_names = explode(',', $_POST['existing_additional_images']);
    }
    if(isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        foreach($_FILES['additional_images']['name'] as $key => $val) {
            if($_FILES['additional_images']['error'][$key] == 0) {
                $ext = pathinfo($_FILES['additional_images']['name'][$key], PATHINFO_EXTENSION);
                $new_name = uniqid('proj_add_') . '.' . $ext;
                move_uploaded_file($_FILES['additional_images']['tmp_name'][$key], $upload_dir . $new_name);
                $additional_images_names[] = $new_name;
            }
        }
    }
    $additional_images_str = implode(',', $additional_images_names);

    if($_POST['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO projects (title, description, tags, link, client, project_date, main_image, additional_images) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $tags, $link, $client, $project_date, $main_image_name, $additional_images_str]);
        header("Location: projects?success=added");
        exit;
    } elseif($_POST['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, tags=?, link=?, client=?, project_date=?, main_image=?, additional_images=? WHERE id=?");
        $stmt->execute([$title, $description, $tags, $link, $client, $project_date, $main_image_name, $additional_images_str, $id]);
        header("Location: projects?success=updated");
        exit;
    }
}

// Handle Delete Project
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $proj = $pdo->prepare("SELECT main_image, additional_images FROM projects WHERE id = ?");
    $proj->execute([$id]);
    $row = $proj->fetch();
    if($row) {
        if($row['main_image'] && file_exists($upload_dir . $row['main_image'])) unlink($upload_dir . $row['main_image']);
        if($row['additional_images']) {
            $imgs = explode(',', $row['additional_images']);
            foreach($imgs as $img) {
                if($img && file_exists($upload_dir . $img)) unlink($upload_dir . $img);
            }
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: projects?success=deleted");
    exit;
}

// Handle Image Deletion
if (isset($_GET['delete_img']) && isset($_GET['pid'])) {
    $img = $_GET['delete_img'];
    $pid = $_GET['pid'];
    
    // Check if it's the main image
    $proj = $pdo->prepare("SELECT main_image, additional_images FROM projects WHERE id = ?");
    $proj->execute([$pid]);
    $row = $proj->fetch();
    
    if($row['main_image'] == $img) {
        if(file_exists($upload_dir . $img)) unlink($upload_dir . $img);
        $pdo->prepare("UPDATE projects SET main_image = NULL WHERE id = ?")->execute([$pid]);
    } else {
        $imgs = explode(',', $row['additional_images']);
        $new_imgs = array_filter($imgs, function($v) use ($img) { return $v != $img; });
        if(file_exists($upload_dir . $img)) unlink($upload_dir . $img);
        $pdo->prepare("UPDATE projects SET additional_images = ? WHERE id = ?")->execute([implode(',', $new_imgs), $pid]);
    }
    header("Location: projects?edit=" . $pid);
    exit;
}

$projects = $pdo->query("SELECT * FROM projects ORDER BY id DESC")->fetchAll();

// If editing
$edit_data = null;
if(isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?= $edit_data ? 'Edit Project' : 'Manage Projects' ?></h1>
            <p class="page-subtitle mb-0">Full CRUD management for your portfolio showcases.</p>
        </div>
        <?php if($edit_data): ?>
            <a href="projects" class="btn-outline-custom"><i class="fa-solid fa-arrow-left me-2"></i> Back to List</a>
        <?php else: ?>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fa-solid fa-plus me-2"></i> Add Project
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
            <input type="hidden" name="project_id" value="<?= $edit_data['id'] ?>">
            <input type="hidden" name="existing_main_image" value="<?= htmlspecialchars($edit_data['main_image'] ?? '') ?>">
            <input type="hidden" name="existing_additional_images" value="<?= htmlspecialchars($edit_data['additional_images'] ?? '') ?>">
            
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <label class="form-label">Project Title *</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($edit_data['title'] ?? '') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Description *</label>
                    <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tech Stack (Comma separated)</label>
                    <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($edit_data['tags'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Project Link</label>
                    <input type="text" name="link" class="form-control" value="<?= htmlspecialchars($edit_data['link'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Client</label>
                    <input type="text" name="client" class="form-control" value="<?= htmlspecialchars($edit_data['client'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Project Date</label>
                    <input type="text" name="project_date" class="form-control" value="<?= htmlspecialchars($edit_data['project_date'] ?? '') ?>">
                </div>
            </div>
            
            <h5 class="text-white fs-6 mb-3 fw-bold border-top border-secondary pt-4">Media Uploads</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Main Project Image (replaces existing)</label>
                    <input type="file" name="main_image" class="form-control" accept="image/jpeg, image/png, image/webp">
                    <?php if($edit_data['main_image']): ?>
                        <div class="mt-2 p-2 border border-secondary rounded" style="background: #111;">
                            <img src="<?= $base_url ?>/assets/images/projects/<?= htmlspecialchars($edit_data['main_image']) ?>" class="img-fluid rounded mb-2" style="max-height: 100px;">
                            <div class="text-end">
                                <a href="?delete_img=<?= $edit_data['main_image'] ?>&pid=<?= $edit_data['id'] ?>" class="text-danger" style="font-size: 0.8rem;" onclick="return confirm('Delete this image?')"><i class="fa-solid fa-trash me-1"></i> Remove</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Additional Images (Multiple)</label>
                    <input type="file" name="additional_images[]" class="form-control" multiple accept="image/jpeg, image/png, image/webp">
                    <small class="text-muted-custom mt-1 d-block" style="font-size: 0.75rem;"><i class="fa-solid fa-circle-info me-1"></i> Tip: Hold down <strong>Ctrl</strong> (Windows) or <strong>Cmd</strong> (Mac) to select multiple images at once.</small>
                    <?php if($edit_data['additional_images']): ?>
                        <div class="mt-3">
                            <span class="text-muted-custom d-block mb-1 small fw-bold">Active Gallery Carousel Images:</span>
                            <div class="p-2 border border-secondary rounded d-flex gap-2 flex-wrap" style="background: #111;">
                                <?php foreach(explode(',', $edit_data['additional_images']) as $img): if(!$img) continue; ?>
                                    <div class="position-relative">
                                        <img src="<?= $base_url ?>/assets/images/projects/<?= htmlspecialchars($img) ?>" class="rounded" style="height: 60px; width: 60px; object-fit: cover;">
                                        <a href="?delete_img=<?= $img ?>&pid=<?= $edit_data['id'] ?>" class="position-absolute top-0 end-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center text-decoration-none" style="width: 20px; height: 20px; font-size: 0.6rem; transform: translate(30%, -30%);" onclick="return confirm('Delete this image?')"><i class="fa-solid fa-xmark"></i></a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-5 text-end">
                <button type="submit" class="btn-primary-custom px-4 py-2">Update Project</button>
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
                    <th>Project Details</th>
                    <th style="width: 150px;">Date</th>
                    <th style="width: 120px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($projects as $proj): ?>
                <tr>
                    <td>
                        <?php if($proj['main_image']): ?>
                            <img src="<?= $base_url ?>/assets/images/projects/<?= htmlspecialchars($proj['main_image']) ?>" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded d-flex align-items-center justify-content-center bg-dark" style="width: 60px; height: 60px; border: 1px solid #333;">
                                <i class="fa-solid fa-image text-muted-custom"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <h6 class="fw-bold mb-1 text-white"><?= htmlspecialchars($proj['title']) ?></h6>
                        <div class="text-muted-custom" style="font-size: 0.8rem;"><?= htmlspecialchars($proj['tags']) ?></div>
                    </td>
                    <td class="text-muted-custom" style="font-size: 0.85rem;"><?= htmlspecialchars($proj['project_date'] ?: 'N/A') ?></td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="?edit=<?= $proj['id'] ?>" class="btn-outline-custom" style="border-color: #333; padding: 6px 12px;">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="?delete=<?= $proj['id'] ?>" class="btn-outline-custom text-danger delete-btn" style="border-color: #333; padding: 6px 12px;">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($projects) == 0): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted-custom py-4">No projects added yet.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="background: #0a0a0a; border: 1px solid #262626; border-radius: 12px;">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold text-white fs-5">Add New Project</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label">Project Title *</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description *</label>
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tech Stack (Comma separated)</label>
                                <input type="text" name="tags" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Project Link</label>
                                <input type="text" name="link" class="form-control" value="#">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Client</label>
                                <input type="text" name="client" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Project Date</label>
                                <input type="text" name="project_date" class="form-control" placeholder="e.g. 06 January, 2023">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Main Image (Max 2MB)</label>
                                <input type="file" name="main_image" class="form-control" accept="image/jpeg, image/png, image/webp">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Additional Images</label>
                                <input type="file" name="additional_images[]" class="form-control" multiple accept="image/jpeg, image/png, image/webp">
                                <small class="text-muted-custom mt-1 d-block" style="font-size: 0.75rem;"><i class="fa-solid fa-circle-info me-1"></i> Tip: Hold down <strong>Ctrl</strong> (Windows) or <strong>Cmd</strong> (Mac) to select multiple images at once.</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-end mt-5">
                            <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-primary-custom">Save Project</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('change', function(e) {
            if (e.target && e.target.name === 'additional_images[]') {
                var input = e.target;
                var fileList = input.files;
                var previewContainer = input.parentElement.querySelector('.selected-files-preview');
                
                if (!previewContainer) {
                    previewContainer = document.createElement('div');
                    previewContainer.className = 'selected-files-preview mt-3 p-3 border border-secondary rounded d-flex gap-2 flex-wrap';
                    previewContainer.style.background = 'rgba(255,255,255,0.02)';
                    input.parentNode.appendChild(previewContainer);
                }
                
                previewContainer.innerHTML = '';
                if (fileList.length === 0) {
                    previewContainer.style.display = 'none';
                    return;
                }
                
                previewContainer.style.display = 'flex';
                
                var title = document.createElement('div');
                title.className = 'w-100 text-white small fw-bold mb-2';
                title.innerHTML = '<i class="fa-solid fa-images me-1 text-primary"></i> Selected Files (' + fileList.length + '):';
                previewContainer.appendChild(title);
                
                Array.from(fileList).forEach(function(file) {
                    var item = document.createElement('div');
                    item.className = 'badge bg-dark border border-secondary p-2 d-flex align-items-center gap-2 text-white';
                    item.style.fontSize = '0.75rem';
                    item.style.fontWeight = '500';
                    
                    var icon = document.createElement('i');
                    icon.className = 'fa-solid fa-image text-muted-custom';
                    item.appendChild(icon);
                    
                    var nameSpan = document.createElement('span');
                    nameSpan.innerText = file.name;
                    item.appendChild(nameSpan);
                    
                    previewContainer.appendChild(item);
                });
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>

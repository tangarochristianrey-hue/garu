<?php
include 'includes/header.php';
$base_url = $base_url ?? '';

$upload_dir = '../assets/images/skills/';

// Handle Add/Edit Skill
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $name = $_POST['name'];
    $percentage = $_POST['percentage'];
    $id = $_POST['id'] ?? null;
    $icon_type = $_POST['icon_type'] ?? 'fontawesome';
    
    $icon_class = '';
    $image_path = '';
    
    if ($icon_type == 'fontawesome') {
        $icon_class = $_POST['icon_class'];
    } elseif ($icon_type == 'url') {
        $image_path = $_POST['image_path_url'];
    } elseif ($icon_type == 'upload') {
        $image_path = $_POST['existing_image_path'] ?? '';
        if (isset($_FILES['skill_image']) && $_FILES['skill_image']['error'] == 0) {
            $ext = pathinfo($_FILES['skill_image']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('skill_') . '.' . $ext;
            if (move_uploaded_file($_FILES['skill_image']['tmp_name'], $upload_dir . $new_name)) {
                // Delete old local file if any
                if (!empty($image_path) && strpos($image_path, 'http') === false && file_exists($upload_dir . $image_path)) {
                    unlink($upload_dir . $image_path);
                }
                $image_path = $new_name;
            }
        }
    }
    
    if($_POST['action'] == 'add') {
        $stmt = $pdo->prepare("INSERT INTO skills (name, percentage, icon_class, image_path, color_class) VALUES (?, ?, ?, ?, '')");
        $stmt->execute([$name, $percentage, $icon_class, $image_path]);
        header("Location: skills?success=added");
        exit;
    } elseif($_POST['action'] == 'edit') {
        $stmt = $pdo->prepare("UPDATE skills SET name=?, percentage=?, icon_class=?, image_path=? WHERE id=?");
        $stmt->execute([$name, $percentage, $icon_class, $image_path, $id]);
        header("Location: skills?success=updated");
        exit;
    }
}

// Handle Delete Skill
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image_path FROM skills WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['image_path']) && strpos($row['image_path'], 'http') === false) {
        $file = $upload_dir . $row['image_path'];
        if (file_exists($file)) {
            unlink($file);
        }
    }
    $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: skills?success=deleted");
    exit;
}

$skills = $pdo->query("SELECT * FROM skills ORDER BY id ASC")->fetchAll();

$edit_data = null;
if(isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_data = $stmt->fetch();
}
?>

    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom border-secondary pb-4">
        <div>
            <h1 class="page-title"><?= $edit_data ? 'Edit Skill' : 'Skills Workspace' ?></h1>
            <p class="page-subtitle mb-0">Manage technical proficiencies, brand icons, and custom logos.</p>
        </div>
        <?php if($edit_data): ?>
            <a href="skills" class="btn-outline-custom"><i class="fa-solid fa-arrow-left me-2"></i> Back</a>
        <?php else: ?>
            <button class="btn-primary-custom px-4 py-2" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                <i class="fa-solid fa-plus me-2"></i> Add Skill
            </button>
        <?php endif; ?>
    </div>
    
    <?php if($edit_data): ?>
    <div class="card-custom mx-auto" style="max-width: 600px;">
        <h5 class="text-white fw-bold mb-4 fs-5">Edit Technical Skill</h5>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
            <input type="hidden" name="existing_image_path" value="<?= htmlspecialchars($edit_data['image_path'] ?? '') ?>">
            
            <div class="mb-4">
                <label class="form-label">Skill Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control py-3" value="<?= htmlspecialchars($edit_data['name']) ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Proficiency Level (0-100) <span class="text-danger">*</span></label>
                <input type="number" name="percentage" class="form-control py-3" min="0" max="100" value="<?= htmlspecialchars($edit_data['percentage']) ?>" required>
            </div>
            
            <?php 
                $active_type = 'fontawesome';
                if (!empty($edit_data['image_path'])) {
                    $active_type = (strpos($edit_data['image_path'], 'http') === 0) ? 'url' : 'upload';
                }
            ?>
            
            <div class="mb-4">
                <label class="form-label">Icon Type</label>
                <select name="icon_type" id="edit_icon_type" class="form-select py-3" onchange="toggleEditFields(this.value)">
                    <option value="fontawesome" <?= $active_type=='fontawesome'?'selected':'' ?>>FontAwesome Brand Icon</option>
                    <option value="upload" <?= $active_type=='upload'?'selected':'' ?>>Upload Custom Logo/Image</option>
                    <option value="url" <?= $active_type=='url'?'selected':'' ?>>External Image URL</option>
                </select>
            </div>
            
            <!-- FontAwesome Option -->
            <div class="mb-5 edit-field-group" id="edit_field_fontawesome" style="display: <?= $active_type=='fontawesome'?'block':'none' ?>;">
                <label class="form-label">FontAwesome Icon Class <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary text-white"><i class="<?= htmlspecialchars($edit_data['icon_class'] ?: 'fa-solid fa-code') ?>"></i></span>
                    <input type="text" name="icon_class" class="form-control py-3" value="<?= htmlspecialchars($edit_data['icon_class']) ?>">
                </div>
            </div>
            
            <!-- Upload Option -->
            <div class="mb-5 edit-field-group" id="edit_field_upload" style="display: <?= $active_type=='upload'?'block':'none' ?>;">
                <label class="form-label">Upload Skill Logo Image</label>
                <input type="file" name="skill_image" class="form-control py-3" accept="image/*">
                <?php if($active_type == 'upload' && !empty($edit_data['image_path'])): ?>
                    <div class="mt-3">
                        <span class="text-muted-custom d-block mb-1" style="font-size:0.8rem;">Current Logo:</span>
                        <img src="<?= $base_url ?>/assets/images/skills/<?= htmlspecialchars($edit_data['image_path']) ?>" class="rounded" style="width: 50px; height: 50px; object-fit: contain; background: #111; padding: 5px; border: 1px solid #333;">
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- URL Option -->
            <div class="mb-5 edit-field-group" id="edit_field_url" style="display: <?= $active_type=='url'?'block':'none' ?>;">
                <label class="form-label">External Image URL <span class="text-danger">*</span></label>
                <input type="text" name="image_path_url" class="form-control py-3" value="<?= $active_type=='url'?htmlspecialchars($edit_data['image_path']):'' ?>" placeholder="https://example.com/logo.png">
            </div>
            
            <div class="d-flex gap-3 justify-content-end">
                <a href="skills" class="btn-outline-custom px-4 py-2">Cancel</a>
                <button type="submit" class="btn-primary-custom px-4 py-2">Save Changes</button>
            </div>
        </form>
    </div>
    
    <script>
        function toggleEditFields(type) {
            document.querySelectorAll('.edit-field-group').forEach(el => {
                el.style.display = 'none';
                el.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
            });
            
            var activeGroup = document.getElementById('edit_field_' + type);
            if (activeGroup) {
                activeGroup.style.display = 'block';
                if (type === 'fontawesome') {
                    activeGroup.querySelector('input[name="icon_class"]').setAttribute('required', 'required');
                } else if (type === 'url') {
                    activeGroup.querySelector('input[name="image_path_url"]').setAttribute('required', 'required');
                } else if (type === 'upload') {
                    var existing = document.querySelector('input[name="existing_image_path"]').value;
                    if (!existing || existing.trim() === '') {
                        activeGroup.querySelector('input[name="skill_image"]').setAttribute('required', 'required');
                    }
                }
            }
        }
        
        // Initialize dynamic edit validation on page load
        document.addEventListener('DOMContentLoaded', function() {
            var iconTypeSelect = document.getElementById('edit_icon_type');
            if (iconTypeSelect) {
                toggleEditFields(iconTypeSelect.value);
            }
        });
    </script>
    
    <?php else: ?>
    <div class="card-custom p-0 overflow-hidden shadow-lg border border-secondary">
        <table class="table mb-0">
            <thead style="background: #111;">
                <tr>
                    <th style="width: 80px;" class="text-center">Icon</th>
                    <th>Skill Name</th>
                    <th style="width: 300px;">Proficiency Matrix</th>
                    <th style="width: 150px;" class="text-end">Management</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($skills as $skill): ?>
                <tr>
                    <td class="text-center">
                        <?php if(!empty($skill['image_path'])): ?>
                            <?php 
                                $src = (strpos($skill['image_path'], 'http') === 0) ? $skill['image_path'] : $base_url . '/assets/images/skills/' . $skill['image_path'];
                            ?>
                            <img src="<?= htmlspecialchars($src) ?>" class="rounded" style="width: 32px; height: 32px; object-fit: contain; vertical-align: middle;">
                        <?php else: ?>
                            <i class="<?= htmlspecialchars($skill['icon_class']) ?> fs-4" style="vertical-align: middle;"></i>
                        <?php endif; ?>
                    </td>
                    <td class="fw-bold fs-6"><?= htmlspecialchars($skill['name']) ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div style="flex-grow: 1; height: 6px; background: #222; border-radius: 6px; overflow: hidden;">
                                <div style="height: 100%; background: #fff; width: <?= htmlspecialchars($skill['percentage']) ?>%;"></div>
                            </div>
                            <span class="fw-bold" style="font-size: 0.85rem; color: #fff; width: 40px;"><?= htmlspecialchars($skill['percentage']) ?>%</span>
                        </div>
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="?edit=<?= $skill['id'] ?>" class="btn-outline-custom border-secondary px-3 py-1" title="Edit">
                                <i class="fa-solid fa-pen" style="font-size: 0.8rem;"></i>
                            </a>
                            <a href="?delete=<?= $skill['id'] ?>" class="btn-outline-custom text-danger border-secondary px-3 py-1 delete-btn" title="Delete">
                                <i class="fa-solid fa-trash-can" style="font-size: 0.8rem;"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($skills) == 0): ?>
                <tr>
                    <td colspan="4" class="text-center text-muted-custom py-5">
                        <i class="fa-solid fa-code fs-1 mb-3"></i>
                        <p>No skills have been mapped yet.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <!-- Modal -->
    <div class="modal fade" id="addSkillModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg" style="background: #0a0a0a; border: 1px solid #333; border-radius: 16px;">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold text-white fs-5">Add New Skill</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-4">
                            <label class="form-label">Skill Name</label>
                            <input type="text" name="name" class="form-control py-3" placeholder="e.g. ReactJS" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Proficiency Level (0-100)</label>
                            <input type="number" name="percentage" class="form-control py-3" min="0" max="100" placeholder="85" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Icon Type</label>
                            <select name="icon_type" id="add_icon_type" class="form-select py-3" onchange="toggleAddFields(this.value)">
                                <option value="fontawesome">FontAwesome Brand Icon</option>
                                <option value="upload">Upload Custom Logo/Image</option>
                                <option value="url">External Image URL</option>
                            </select>
                        </div>
                        
                        <!-- FontAwesome Option -->
                        <div class="mb-5 add-field-group" id="add_field_fontawesome">
                            <label class="form-label">FontAwesome Icon Class</label>
                            <input type="text" name="icon_class" class="form-control py-3" placeholder="e.g. fa-brands fa-react">
                        </div>
                        
                        <!-- Upload Option -->
                        <div class="mb-5 add-field-group" id="add_field_upload" style="display: none;">
                            <label class="form-label">Upload Skill Logo Image</label>
                            <input type="file" name="skill_image" class="form-control py-3" accept="image/*">
                        </div>
                        
                        <!-- URL Option -->
                        <div class="mb-5 add-field-group" id="add_field_url" style="display: none;">
                            <label class="form-label">External Image URL</label>
                            <input type="text" name="image_path_url" class="form-control py-3" placeholder="https://example.com/logo.png">
                        </div>
                        
                        <div class="d-flex gap-3 justify-content-end">
                            <button type="button" class="btn-outline-custom px-4 py-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn-primary-custom px-4 py-2">Deploy Skill</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function toggleAddFields(type) {
            document.querySelectorAll('.add-field-group').forEach(el => {
                el.style.display = 'none';
                el.querySelectorAll('input').forEach(input => input.removeAttribute('required'));
            });
            
            var activeGroup = document.getElementById('add_field_' + type);
            if (activeGroup) {
                activeGroup.style.display = 'block';
                if (type === 'fontawesome') {
                    activeGroup.querySelector('input[name="icon_class"]').setAttribute('required', 'required');
                } else if (type === 'url') {
                    activeGroup.querySelector('input[name="image_path_url"]').setAttribute('required', 'required');
                } else if (type === 'upload') {
                    activeGroup.querySelector('input[name="skill_image"]').setAttribute('required', 'required');
                }
            }
        }
        
        // Initialize dynamic add validation on page load
        document.addEventListener('DOMContentLoaded', function() {
            var iconTypeSelect = document.getElementById('add_icon_type');
            if (iconTypeSelect) {
                toggleAddFields(iconTypeSelect.value);
            }
        });
    </script>

<?php include 'includes/footer.php'; ?>

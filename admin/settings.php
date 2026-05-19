<?php
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $settings_to_update = ['hero_title', 'hero_desc', 'about_text', 'email', 'phone', 'location', 'education_short', 'education_school', 'linkedin', 'facebook', 'github', 'full_name', 'birthdate'];
    
    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    foreach ($settings_to_update as $key) {
        if(isset($_POST[$key])) {
            $stmt->execute([$_POST[$key], $key]);
        }
    }
    header("Location: settings?success=1");
    exit;
}

$stmt = $pdo->query("SELECT * FROM settings");
$settings_data = [];
while($row = $stmt->fetch()) {
    $settings_data[$row['setting_key']] = $row['setting_value'];
}
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">General Settings</h1>
            <p class="page-subtitle mb-0">Update the core text and contact information across your portfolio.</p>
        </div>
    </div>
    
    <?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success bg-dark border-success text-success p-2 mb-4" style="font-size: 0.85rem;">Settings updated successfully.</div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="action" value="update">
        
        <div class="card-custom mb-4">
            <h5 class="fw-bold mb-4 fs-6">Personal Details</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($settings_data['full_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input type="text" name="birthdate" class="form-control" value="<?= htmlspecialchars($settings_data['birthdate'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($settings_data['location'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">LinkedIn URL</label>
                    <input type="text" name="linkedin" class="form-control" value="<?= htmlspecialchars($settings_data['linkedin'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Facebook URL</label>
                    <input type="text" name="facebook" class="form-control" value="<?= htmlspecialchars($settings_data['facebook'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">GitHub URL</label>
                    <input type="text" name="github" class="form-control" value="<?= htmlspecialchars($settings_data['github'] ?? '') ?>">
                </div>
            </div>
        </div>
        
        <div class="card-custom mb-4">
            <h5 class="fw-bold mb-4 fs-6">Contact Information</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($settings_data['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($settings_data['phone'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="card-custom mb-4">
            <h5 class="fw-bold mb-4 fs-6">Hero & About Section</h5>
            <div class="row g-4">
                <div class="col-12">
                    <label class="form-label">Hero Title (Press Enter for new lines)</label>
                    <textarea name="hero_title" class="form-control" rows="3"><?= htmlspecialchars($settings_data['hero_title'] ?? '') ?></textarea>
                    <small class="text-muted-custom mt-1 d-block" style="font-size: 0.75rem;">The very last line you type will automatically be highlighted in silver.</small>
                </div>
                <div class="col-12">
                    <label class="form-label">Hero Description</label>
                    <textarea name="hero_desc" class="form-control" rows="3"><?= htmlspecialchars($settings_data['hero_desc'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">About Me Text</label>
                    <textarea name="about_text" class="form-control" rows="5"><?= htmlspecialchars($settings_data['about_text'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-custom mb-5">
            <h5 class="fw-bold mb-4 fs-6">Education (About Section)</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Degree / Short Title</label>
                    <input type="text" name="education_short" class="form-control" value="<?= htmlspecialchars($settings_data['education_short'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">School Name</label>
                    <input type="text" name="education_school" class="form-control" value="<?= htmlspecialchars($settings_data['education_school'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-5">
            <button type="submit" class="btn-primary-custom px-5 py-3 fs-6">Save All Settings</button>
        </div>
    </form>

<?php include 'includes/footer.php'; ?>

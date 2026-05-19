<?php
include 'includes/header.php';

// Fallback guard — $base_url is always set by includes/db.php via header.php
if (!isset($base_url)) {
    $is_localhost = isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1' || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));
    $base_url = $is_localhost ? '/garu' : '';
}

// Handle actions (Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $name = $_POST['name'];
        $email = $_POST['email'];
        $rating = intval($_POST['rating']);
        $comment = $_POST['comment'];
        
        $stmt = $pdo->prepare("UPDATE ratings SET name=?, email=?, rating=?, comment=? WHERE id=?");
        $stmt->execute([$name, $email, $rating, $comment, $id]);
        header("Location: reviews?success=updated");
        exit;
    }
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    if ($action == 'delete') {
        $pdo->prepare("DELETE FROM ratings WHERE id = ?")->execute([$id]);
        header("Location: reviews?success=deleted");
        exit;
    }
}

// Fetch all reviews
$reviews = $pdo->query("SELECT * FROM ratings ORDER BY id DESC")->fetchAll();
$total_reviews = count($reviews);

// Calculate average
$avg_rating = 0;
if ($total_reviews > 0) {
    $sum = 0;
    foreach ($reviews as $r) {
        $sum += $r['rating'];
    }
    $avg_rating = $sum / $total_reviews;
}
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Community Reviews</h1>
            <p class="page-subtitle mb-0">Monitor, edit, and moderate user ratings and comments left by visitors.</p>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card-custom d-flex justify-content-between align-items-center py-4">
                <div>
                    <h6 class="text-muted-custom small text-uppercase fw-bold mb-2">Total Reviews</h6>
                    <h2 class="text-white fw-bold mb-0"><?= $total_reviews ?></h2>
                </div>
                <div class="fs-1 text-muted-custom"><i class="fa-solid fa-comments"></i></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom d-flex justify-content-between align-items-center py-4">
                <div>
                    <h6 class="text-muted-custom small text-uppercase fw-bold mb-2">Average Score</h6>
                    <h2 class="text-warning fw-bold mb-0"><?= number_format($avg_rating, 1) ?> <span style="font-size: 1rem; font-weight: normal; color: var(--text-secondary);">/ 5.0</span></h2>
                </div>
                <div class="fs-1 text-warning"><i class="fa-solid fa-star"></i></div>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card-custom p-0 overflow-hidden mb-5">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="width: 220px;">User</th>
                    <th style="width: 140px;">Rating</th>
                    <th>Comment</th>
                    <th style="width: 180px;">Date Submitted</th>
                    <th style="width: 150px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reviews as $rev): 
                    $stars_color = '#eab308';
                    if ($rev['rating'] <= 2) $stars_color = '#ef4444';
                    elseif ($rev['rating'] == 3) $stars_color = '#f97316';
                ?>
                <tr>
                    <td>
                        <div class="fw-bold text-white">
                            <?= htmlspecialchars($rev['name']) ?>
                        </div>
                        <div class="text-muted-custom small" style="font-size: 0.8rem;">
                            <?= htmlspecialchars($rev['email']) ?>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-0.5 text-warning small">
                            <?php
                            for($i=0; $i<$rev['rating']; $i++) echo '<i class="fa-solid fa-star" style="color: ' . $stars_color . ';"></i>';
                            for($i=0; $i<(5 - $rev['rating']); $i++) echo '<i class="fa-regular fa-star" style="color: rgba(255,255,255,0.15);"></i>';
                            ?>
                        </div>
                    </td>
                    <td>
                        <div class="text-secondary-custom small italic" style="line-height: 1.5; font-style: italic;">
                            "<?= htmlspecialchars($rev['comment']) ?>"
                        </div>
                    </td>
                    <td class="text-muted-custom" style="font-size: 0.85rem;">
                        <?= date('M d, Y h:i A', strtotime($rev['created_at'])) ?>
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn-outline-custom border-secondary px-3 py-1 edit-review-btn" 
                                    data-id="<?= $rev['id'] ?>" 
                                    data-name="<?= htmlspecialchars($rev['name']) ?>" 
                                    data-email="<?= htmlspecialchars($rev['email']) ?>" 
                                    data-rating="<?= $rev['rating'] ?>" 
                                    data-comment="<?= htmlspecialchars($rev['comment']) ?>"
                                    title="Edit Review">
                                <i class="fa-solid fa-pen-to-square" style="font-size: 0.8rem;"></i>
                            </button>
                            
                            <a href="?action=delete&id=<?= $rev['id'] ?>" class="btn-outline-custom text-danger border-secondary px-3 py-1 delete-review-btn" title="Delete Review">
                                <i class="fa-solid fa-trash-can" style="font-size: 0.8rem;"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if($total_reviews == 0): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted-custom py-5">
                        <i class="fa-solid fa-comments fs-1 mb-3"></i>
                        <p>No reviews submitted yet.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit Review Modal -->
    <div class="modal fade" id="editReviewModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #0a0a0a; border: 1px solid #262626; border-radius: 12px;">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold text-white fs-5">Edit Review Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="editReviewId">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-secondary-custom">Reviewer Name</label>
                            <input type="text" name="name" id="editReviewName" class="form-control bg-dark border-secondary text-white" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary-custom">Reviewer Email</label>
                            <input type="email" name="email" id="editReviewEmail" class="form-control bg-dark border-secondary text-white" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-secondary-custom">Rating (1 to 5 Stars)</label>
                            <select name="rating" id="editReviewRating" class="form-select bg-dark border-secondary text-white" required>
                                <option value="5">5 Stars</option>
                                <option value="4">4 Stars</option>
                                <option value="3">3 Stars</option>
                                <option value="2">2 Stars</option>
                                <option value="1">1 Star</option>
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label text-secondary-custom">Comment</label>
                            <textarea name="comment" id="editReviewComment" class="form-control bg-dark border-secondary text-white" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-secondary p-4 flex-wrap gap-2 justify-content-end">
                        <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-primary-custom">Save Changes <i class="fa-solid fa-check ms-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Review Modal Populate Logic
        const editButtons = document.querySelectorAll('.edit-review-btn');
        const editModal = new bootstrap.Modal(document.getElementById('editReviewModal'));
        
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const rating = this.getAttribute('data-rating');
                const comment = this.getAttribute('data-comment');
                
                document.getElementById('editReviewId').value = id;
                document.getElementById('editReviewName').value = name;
                document.getElementById('editReviewEmail').value = email;
                document.getElementById('editReviewRating').value = rating;
                document.getElementById('editReviewComment').value = comment;
                
                editModal.show();
            });
        });
        
        // SweetAlert2 Confirmation for Deletion
        const deleteButtons = document.querySelectorAll('.delete-review-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                
                Swal.fire({
                    title: 'Delete review?',
                    text: "You won't be able to recover this rating & comment!",
                    icon: 'warning',
                    showCancelButton: true,
                    background: '#0a0a0a',
                    color: '#fff',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#1f1f1f',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
        
        // Success Toasts
        <?php if(isset($_GET['success'])): ?>
            const successType = "<?= htmlspecialchars($_GET['success']) ?>";
            let msg = "";
            if(successType === 'updated') msg = "Review details updated successfully.";
            if(successType === 'deleted') msg = "Review has been permanently deleted.";
            
            if(msg) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: msg,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#0a0a0a',
                    color: '#fff'
                });
            }
        <?php endif; ?>
    });
    </script>

<?php include 'includes/footer.php'; ?>

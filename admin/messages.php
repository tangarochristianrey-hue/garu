<?php
include 'includes/header.php';

// Handle Actions (Mark Read, Mark Unread, Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);
    
    if ($action == 'read') {
        $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?")->execute([$id]);
        header("Location: messages?success=read");
        exit;
    } elseif ($action == 'unread') {
        $pdo->prepare("UPDATE messages SET is_read = 0 WHERE id = ?")->execute([$id]);
        header("Location: messages?success=unread");
        exit;
    } elseif ($action == 'delete') {
        $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([$id]);
        header("Location: messages?success=deleted");
        exit;
    }
}

// Fetch all messages
$messages = $pdo->query("SELECT * FROM messages ORDER BY id DESC")->fetchAll();

// Get summary stats
$total_messages = count($messages);
$unread_messages = 0;
foreach($messages as $m) {
    if($m['is_read'] == 0) $unread_messages++;
}
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Client Messages</h1>
            <p class="page-subtitle mb-0">Read and manage inquiries sent through your portfolio contact form.</p>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card-custom d-flex justify-content-between align-items-center py-4">
                <div>
                    <h6 class="text-muted-custom small text-uppercase fw-bold mb-2">Total Inquiries</h6>
                    <h2 class="text-white fw-bold mb-0"><?= $total_messages ?></h2>
                </div>
                <div class="fs-1 text-muted-custom"><i class="fa-solid fa-inbox"></i></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom d-flex justify-content-between align-items-center py-4">
                <div>
                    <h6 class="text-muted-custom small text-uppercase fw-bold mb-2">Unread Messages</h6>
                    <h2 class="text-danger fw-bold mb-0"><?= $unread_messages ?></h2>
                </div>
                <div class="fs-1 text-danger"><i class="fa-solid fa-envelope-open-text"></i></div>
            </div>
        </div>
    </div>

    <!-- Messages List -->
    <div class="card-custom p-0 overflow-hidden mb-5">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th style="width: 250px;">Sender</th>
                    <th>Subject</th>
                    <th style="width: 200px;">Date Received</th>
                    <th style="width: 120px;">Status</th>
                    <th style="width: 180px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($messages as $m): ?>
                <tr style="<?= $m['is_read'] == 0 ? 'background: rgba(255,255,255,0.01); font-weight: 500;' : '' ?>">
                    <td>
                        <div class="fw-bold <?= $m['is_read'] == 0 ? 'text-white' : 'text-secondary-custom' ?>">
                            <?= htmlspecialchars($m['name'] ?? '') ?>
                        </div>
                        <div class="text-muted-custom" style="font-size: 0.8rem;">
                            <?= htmlspecialchars($m['email'] ?? '') ?>
                        </div>
                    </td>
                    <td>
                        <span class="<?= $m['is_read'] == 0 ? 'text-white' : 'text-muted-custom' ?>">
                            <?= htmlspecialchars($m['subject'] ?? '(No Subject)') ?>
                        </span>
                        <div class="text-muted-custom text-truncate" style="max-width: 400px; font-size: 0.8rem; font-weight: normal;">
                            <?= htmlspecialchars($m['message'] ?? '') ?>
                        </div>
                    </td>
                    <td class="text-muted-custom" style="font-size: 0.85rem;">
                        <?= date('M d, Y h:i A', strtotime($m['created_at'])) ?>
                    </td>
                    <td>
                        <?php if($m['is_read'] == 0): ?>
                            <span class="badge bg-danger px-2.5 py-1.5" style="border-radius: 4px; font-size: 0.7rem; font-weight: 600;">UNREAD</span>
                        <?php else: ?>
                            <span class="badge bg-dark border border-secondary text-secondary px-2.5 py-1.5" style="border-radius: 4px; font-size: 0.7rem; font-weight: 500;">READ</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <!-- View Modal Button -->
                            <button class="btn-outline-custom border-secondary px-3 py-1 view-msg-btn" 
                                    data-id="<?= $m['id'] ?>" 
                                    data-name="<?= htmlspecialchars($m['name']) ?>" 
                                    data-email="<?= htmlspecialchars($m['email']) ?>" 
                                    data-subject="<?= htmlspecialchars($m['subject']) ?>" 
                                    data-message="<?= htmlspecialchars($m['message']) ?>" 
                                    data-date="<?= date('M d, Y h:i A', strtotime($m['created_at'])) ?>"
                                    data-read="<?= $m['is_read'] ?>"
                                    title="Read Message">
                                <i class="fa-solid fa-eye" style="font-size: 0.8rem;"></i>
                            </button>
                            
                            <?php if($m['is_read'] == 0): ?>
                                <a href="?action=read&id=<?= $m['id'] ?>" class="btn-outline-custom border-secondary px-3 py-1 text-success" title="Mark as Read">
                                    <i class="fa-solid fa-check" style="font-size: 0.8rem;"></i>
                                </a>
                            <?php else: ?>
                                <a href="?action=unread&id=<?= $m['id'] ?>" class="btn-outline-custom border-secondary px-3 py-1 text-warning" title="Mark as Unread">
                                    <i class="fa-solid fa-envelope" style="font-size: 0.8rem;"></i>
                                </a>
                            <?php endif; ?>
                            
                            <a href="?action=delete&id=<?= $m['id'] ?>" class="btn-outline-custom text-danger border-secondary px-3 py-1 delete-msg-btn" title="Delete Inquiry">
                                <i class="fa-solid fa-trash-can" style="font-size: 0.8rem;"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if($total_messages == 0): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted-custom py-5">
                        <i class="fa-solid fa-inbox fs-1 mb-3"></i>
                        <p>Your inbox is clean! No messages received yet.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Message Detail Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: #0a0a0a; border: 1px solid #262626; border-radius: 12px;">
                <div class="modal-header border-bottom border-secondary p-4">
                    <h5 class="modal-title fw-bold text-white fs-5" id="modalSubject">Message Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <div class="text-muted-custom small text-uppercase fw-bold mb-1">From</div>
                        <h6 class="text-white fw-bold mb-0" id="modalSenderName"></h6>
                        <span class="text-muted-custom small" id="modalSenderEmail"></span>
                    </div>
                    <div class="mb-4">
                        <div class="text-muted-custom small text-uppercase fw-bold mb-1">Received Date</div>
                        <span class="text-white small" id="modalDate"></span>
                    </div>
                    <div class="mb-1">
                        <div class="text-muted-custom small text-uppercase fw-bold mb-1">Message</div>
                        <div class="p-3 rounded text-white" style="background: #111; border: 1px solid #222; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;" id="modalContent"></div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary p-4 flex-wrap gap-2 justify-content-end">
                    <button type="button" class="btn-outline-custom" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="modalReplyBtn" class="btn-primary-custom text-decoration-none">Reply <i class="fa-solid fa-reply ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Message Details Modal Logic
        const viewButtons = document.querySelectorAll('.view-msg-btn');
        const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
        
        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const email = this.getAttribute('data-email');
                const subject = this.getAttribute('data-subject') || '(No Subject)';
                const message = this.getAttribute('data-message');
                const date = this.getAttribute('data-date');
                const isRead = this.getAttribute('data-read');
                
                document.getElementById('modalSubject').textContent = subject;
                document.getElementById('modalSenderName').textContent = name;
                document.getElementById('modalSenderEmail').textContent = email;
                document.getElementById('modalDate').textContent = date;
                document.getElementById('modalContent').textContent = message;
                document.getElementById('modalReplyBtn').setAttribute('href', 'mailto:' + email + '?subject=Re: ' + encodeURIComponent(subject));
                
                messageModal.show();
                
                // If the message was unread, mark it as read dynamically
                if (isRead == '0') {
                    // Redirect to mark as read after modal closes
                    document.getElementById('messageModal').addEventListener('hidden.bs.modal', function () {
                        window.location.href = '?action=read&id=' + id;
                    }, { once: true });
                }
            });
        });
        
        // SweetAlert2 Confirmation for Deletion
        const deleteButtons = document.querySelectorAll('.delete-msg-btn');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                
                Swal.fire({
                    title: 'Delete inquiry?',
                    text: "You won't be able to recover this message!",
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
            if(successType === 'read') msg = "Message marked as read.";
            if(successType === 'unread') msg = "Message marked as unread.";
            if(successType === 'deleted') msg = "Inquiry has been deleted.";
            
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

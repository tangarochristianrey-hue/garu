<?php include 'includes/header.php'; ?>
<?php
// Fetch dynamic community ratings average statistics
$stats_stmt = $pdo->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM ratings");
$stats = $stats_stmt->fetch();
$avg_rating = $stats['avg_rating'] !== null ? round($stats['avg_rating'], 1) : 0.0;
$total_ratings = $stats['total_ratings'] ?? 0;

// Fetch all review comments to display in feed
$reviews_stmt = $pdo->query("SELECT * FROM ratings ORDER BY created_at DESC");
$reviews = $reviews_stmt->fetchAll();

$success_message = false;
$error_message = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'submit_rating') {
        $name = trim($_POST['name'] ?? '');
        $email_input = trim($_POST['email'] ?? '');
        $rating = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');
        
        if (!empty($name) && !empty($email_input) && $rating >= 1 && $rating <= 5 && !empty($comment)) {
            if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Please enter a valid, active email address.";
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO ratings (name, email, rating, comment) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $email_input, $rating, $comment]);
                    
                    // Re-calculate statistics instantly
                    $stats_stmt = $pdo->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings FROM ratings");
                    $stats = $stats_stmt->fetch();
                    $avg_rating = $stats['avg_rating'] !== null ? round($stats['avg_rating'], 1) : 0.0;
                    $total_ratings = $stats['total_ratings'] ?? 0;
                    
                    // Re-fetch reviews to display new comment instantly
                    $reviews_stmt = $pdo->query("SELECT * FROM ratings ORDER BY created_at DESC");
                    $reviews = $reviews_stmt->fetchAll();
                    
                    $success_message = "Thank you so much for your rating and feedback! An appreciation email has been sent to your inbox.";
                    
                    // Deciding Tailored Professional Email content based on Stars Rating
                    $stars_string = str_repeat("★", $rating) . str_repeat("☆", 5 - $rating);
                    if ($rating >= 4) {
                        $emailSubject = "Thank you for the wonderful review! - Christian Rey M. Tangaro";
                        $feedback_heading = "You're absolutely amazing!";
                        $feedback_body = "I am thrilled and deeply grateful for your outstanding <strong>" . $rating . "/5 star review</strong>! Knowing that my portfolio and digital solutions made such a positive impact on you is incredibly rewarding. Your support motivates me to continue learning, innovating, and building high-performance systems. I wish you phenomenal success in all your future endeavors!";
                    } elseif ($rating == 3) {
                        $emailSubject = "Thank you for your valuable feedback! - Christian Rey M. Tangaro";
                        $feedback_heading = "Thank you for helping me grow!";
                        $feedback_body = "Thank you for sharing your balanced <strong>" . $rating . "/5 star review</strong>. Your comment is extremely valuable to me as an Information Systems student. I am always striving to refine my projects and deliver a 5-star experience. If you have any specific recommendations or features you'd love to see improved, please feel free to reply directly to this email!";
                    } else {
                        $emailSubject = "We value your honest feedback - Christian Rey M. Tangaro";
                        $feedback_heading = "We appreciate your honesty!";
                        $feedback_body = "Thank you for taking the time to share your honest <strong>" . $rating . "/5 star review</strong>. I sincerely apologize that my portfolio did not meet your expectations. Constructive feedback is the most powerful tool for improvement, and I will be using your review to refine my skills, fix layouts, and optimize future applications. If you would like to discuss your experience further, feel free to reply directly to this email.";
                    }
                    
                    $htmlBody = '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            body { font-family: \'Inter\', sans-serif; background-color: #080808; color: #ffffff; padding: 40px 20px; margin: 0; }
                            .email-container { max-width: 600px; background-color: #121212; border: 1px solid #222; border-radius: 16px; padding: 40px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
                            .header { text-align: center; margin-bottom: 30px; }
                            .header h1 { font-family: \'Space Grotesk\', sans-serif; font-size: 24px; margin: 0; color: #ffffff; }
                            .divider { height: 1px; background-color: #222; margin: 25px 0; }
                            .greeting { font-size: 16px; line-height: 1.6; color: #e2e8f0; }
                            .rating-badge { font-size: 22px; color: #eab308; margin: 15px 0; text-align: center; letter-spacing: 2px; }
                            .message-box { background-color: #181818; border: 1px solid #2a2a2a; border-radius: 12px; padding: 20px; margin: 20px 0; }
                            .message-label { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 6px; letter-spacing: 1px; }
                            .message-content { font-size: 14px; color: #cbd5e1; line-height: 1.5; margin: 0; }
                            .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #64748b; }
                            .footer p { margin: 4px 0; }
                            .highlight { color: #ffffff; font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <div class="header">
                                <h1>Christian Rey M. Tangaro</h1>
                                <p style="color: #64748b; font-size: 13px; margin: 5px 0 0 0;">Digital Solutions &amp; Web Development</p>
                            </div>
                            <div class="divider"></div>
                            <div class="greeting">
                                Hello <span class="highlight">' . htmlspecialchars($name) . '</span>,<br><br>
                                ' . $feedback_heading . '<br><br>
                                ' . $feedback_body . '
                            </div>
                            
                            <div class="rating-badge">' . $stars_string . '</div>
                            
                            <div class="message-box">
                                <div class="message-label">Your Submitted Comment</div>
                                <p class="message-content">"' . nl2br(htmlspecialchars($comment)) . '"</p>
                            </div>
                            
                            <div class="greeting">
                                Best regards,<br>
                                <span class="highlight">Christian Rey M. Tangaro</span>
                            </div>
                            <div class="divider"></div>
                            <div class="footer">
                                <p>&copy; ' . date('Y') . ' Christian Rey M. Tangaro. All rights reserved.</p>
                                <p>Panabo City, Davao del Norte, Philippines</p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ';
                    
                    // Trigger Brevo SMTP API Email to User (Appreciation Email)
                    sendBrevoEmail($email_input, $name, $emailSubject, $htmlBody);
                    
                    // Trigger Alert Email to Admin
                    $adminEmail = 'tangarochristianrey@gmail.com';
                    $adminSubject = "⭐ New " . $rating . "-Star Review from " . htmlspecialchars($name);
                    $adminBody = '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            body { font-family: \'Inter\', sans-serif; background-color: #080808; color: #ffffff; padding: 40px 20px; margin: 0; }
                            .email-container { max-width: 600px; background-color: #121212; border: 1px solid #222; border-radius: 16px; padding: 40px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
                            .header { text-align: center; margin-bottom: 30px; }
                            .header h1 { font-family: \'Space Grotesk\', sans-serif; font-size: 22px; margin: 0; color: #ffffff; }
                            .divider { height: 1px; background-color: #222; margin: 25px 0; }
                            .rating-badge { font-size: 24px; color: #eab308; margin: 15px 0; text-align: center; letter-spacing: 2px; }
                            .message-box { background-color: #181818; border: 1px solid #2a2a2a; border-radius: 12px; padding: 20px; margin: 20px 0; }
                            .message-label { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 6px; letter-spacing: 1px; }
                            .highlight { color: #ffffff; font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <div class="header">
                                <h1>New Review Notification</h1>
                                <p style="color: #64748b; font-size: 13px; margin: 5px 0 0 0;">Community Wall Feedback Terminal</p>
                            </div>
                            <div class="divider"></div>
                            
                            <div style="font-size: 15px; color: #cbd5e1; line-height: 1.6;">
                                Hello Christian,<br><br>
                                A visitor has posted a new review and rating on your portfolio. Here are the details:
                            </div>
                            
                            <div class="rating-badge">' . $stars_string . ' (' . $rating . '/5 Stars)</div>
                            
                            <div class="message-box">
                                <div class="message-label">Reviewer Details</div>
                                <div style="margin-bottom: 10px; font-size: 13px; color: #cbd5e1;">
                                    <strong>Name:</strong> <span class="highlight">' . htmlspecialchars($name) . '</span><br>
                                    <strong>Email:</strong> <span class="highlight">' . htmlspecialchars($email_input) . '</span>
                                </div>
                                <div class="divider" style="margin: 15px 0; border-style: dashed;"></div>
                                <div class="message-label">Review Comment</div>
                                <p style="font-size: 13px; color: #cbd5e1; line-height: 1.5; margin: 0; font-style: italic;">"' . nl2br(htmlspecialchars($comment)) . '"</p>
                            </div>
                            
                            <div style="text-align: center; margin-top: 30px;">
                                <a href="https://garu.rf.gd/admin/reviews" target="_blank" style="background: #ffffff; color: #000000; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 13px; font-weight: bold; display: inline-block;">Manage Reviews</a>
                            </div>
                        </div>
                    </body>
                    </html>
                    ';
                    sendBrevoEmail($adminEmail, 'Christian Rey M. Tangaro', $adminSubject, $adminBody);
                } catch (PDOException $e) {
                    $error_message = "Failed to submit review. Please try again.";
                }
            }
        } else {
            $error_message = "All fields are required. Please select a star rating.";
        }
    }
}
?>

    <section id="reviews" style="padding-top: 120px; min-height: 100vh; padding-bottom: 80px;">
        <div class="container">

            <!-- Full-Width Page Header -->
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <div class="section-label reveal">REVIEWS CENTER</div>
                    <h1 class="section-title reveal" style="font-size: 2.5rem; letter-spacing: -0.03em;">Community Feedback</h1>
                    <p class="hero-desc reveal mx-auto" style="max-width: 560px; margin-bottom: 0;">
                        Real ratings and comments from developers, clients, and visitors who have explored my portfolio and digital work.
                    </p>
                </div>
            </div>

            <div class="row g-4 align-items-start">
                <!-- Left Side: Stats + Form -->
                <div class="col-lg-5">
                    
                    <!-- Average Score Card -->
                    <div class="info-card d-flex flex-column align-items-center justify-content-center text-center p-4 mb-4 shadow-lg rounded-4" style="height: auto !important;">
                        <div class="section-label mb-1">AVERAGE SCORE</div>
                        <div class="fw-bold text-white mb-2" style="font-family: 'Space Grotesk', sans-serif; font-size: 3.2rem; line-height: 1;">
                            <?= number_format($avg_rating, 1) ?>
                        </div>
                        <div class="d-flex gap-1 text-warning mb-2" style="font-size: 1.3rem;">
                            <?php
                            $full_stars = floor($avg_rating);
                            $half_star = ($avg_rating - $full_stars) >= 0.5 ? 1 : 0;
                            $empty_stars = 5 - $full_stars - $half_star;
                            for($i=0; $i<$full_stars; $i++) echo '<i class="fa-solid fa-star"></i>';
                            if($half_star) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                            for($i=0; $i<$empty_stars; $i++) echo '<i class="fa-regular fa-star"></i>';
                            ?>
                        </div>
                        <span style="color: var(--text-secondary); font-size: 0.8rem;">
                            <?= $total_ratings ?> rating<?= $total_ratings == 1 ? '' : 's' ?> submitted
                        </span>
                    </div>

                    <!-- Write a Review Form -->
                    <div class="info-card p-4 shadow-lg rounded-4" style="height: auto !important;">
                        <h2 class="fw-bold text-white mb-4" style="font-size: 1.2rem; letter-spacing: -0.5px;">
                            <i class="fa-solid fa-pen-to-square me-2" style="color: var(--text-secondary); font-size: 1rem;"></i>Write a Review
                        </h2>
                        <form action="" method="POST">
                            <input type="hidden" name="action" value="submit_rating">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label" style="color: var(--text-secondary); font-size: 0.8rem; font-weight: 500;">Your Name</label>
                                    <input type="text" name="name" class="form-control" required placeholder="John Doe">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" style="color: var(--text-secondary); font-size: 0.8rem; font-weight: 500;">Your Email</label>
                                    <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                                </div>
                                <div class="col-12">
                                    <label class="form-label d-block mb-2" style="color: var(--text-secondary); font-size: 0.8rem; font-weight: 500;">Star Rating</label>
                                    <div class="star-rating-widget d-flex gap-2">
                                        <input type="radio" name="rating" id="star1" value="1" class="d-none" required>
                                        <label for="star1" class="fa-solid fa-star star-btn" style="font-size: 1.6rem; cursor: pointer; color: rgba(255,255,255,0.15); transition: all 0.2s;"></label>
                                        <input type="radio" name="rating" id="star2" value="2" class="d-none">
                                        <label for="star2" class="fa-solid fa-star star-btn" style="font-size: 1.6rem; cursor: pointer; color: rgba(255,255,255,0.15); transition: all 0.2s;"></label>
                                        <input type="radio" name="rating" id="star3" value="3" class="d-none">
                                        <label for="star3" class="fa-solid fa-star star-btn" style="font-size: 1.6rem; cursor: pointer; color: rgba(255,255,255,0.15); transition: all 0.2s;"></label>
                                        <input type="radio" name="rating" id="star4" value="4" class="d-none">
                                        <label for="star4" class="fa-solid fa-star star-btn" style="font-size: 1.6rem; cursor: pointer; color: rgba(255,255,255,0.15); transition: all 0.2s;"></label>
                                        <input type="radio" name="rating" id="star5" value="5" class="d-none">
                                        <label for="star5" class="fa-solid fa-star star-btn" style="font-size: 1.6rem; cursor: pointer; color: rgba(255,255,255,0.15); transition: all 0.2s;"></label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" style="color: var(--text-secondary); font-size: 0.8rem; font-weight: 500;">Your Comments</label>
                                    <textarea name="comment" class="form-control" rows="4" required placeholder="Tell me what you think..."></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-primary-custom w-100" style="padding: 14px 0;">
                                        Submit Review &nbsp;<i class="fa-solid fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Right Side: Community Reviews Feed -->
                <div class="col-lg-7">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <div class="section-label">COMMUNITY WALL</div>
                            <h2 class="section-title mb-0">What People Say</h2>
                        </div>
                        <?php if ($total_ratings > 0): ?>
                        <span class="badge px-3 py-2 rounded-pill" style="background: rgba(255,255,255,0.06); color: var(--text-secondary); font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.08);">
                            <?= $total_ratings ?> review<?= $total_ratings == 1 ? '' : 's' ?>
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="reviews-feed-wrapper">
                        <div class="reviews-feed-container d-flex flex-column gap-3">
                            <?php if (empty($reviews)): ?>
                                <div class="info-card p-5 text-center rounded-4" style="height: auto !important;">
                                    <i class="fa-regular fa-comments fa-3x mb-3" style="opacity: 0.3; color: var(--text-secondary);"></i>
                                    <h4 class="text-white fw-bold mb-2">No reviews yet</h4>
                                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">Be the very first to submit a rating and comment on Christian's work!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($reviews as $rev): 
                                    $stars_color = '#eab308';
                                    if ($rev['rating'] <= 2) $stars_color = '#ef4444';
                                    elseif ($rev['rating'] == 3) $stars_color = '#f97316';
                                ?>
                                    <div class="review-bubble-card p-4 rounded-4" style="border-left: 3px solid <?= $stars_color ?> !important; border: 1px solid rgba(255,255,255,0.07);">
                                        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                                            <div>
                                                <h5 class="text-white fw-bold mb-1" style="font-size: 0.95rem;"><?= htmlspecialchars($rev['name']) ?></h5>
                                                <span style="color: var(--text-secondary); font-size: 0.75rem;">
                                                    <i class="fa-regular fa-clock me-1"></i><?= date('F j, Y', strtotime($rev['created_at'])) ?>
                                                </span>
                                            </div>
                                            <div class="d-flex gap-1" style="font-size: 0.9rem;">
                                                <?php
                                                for($i=0; $i<$rev['rating']; $i++) echo '<i class="fa-solid fa-star" style="color: ' . $stars_color . ';"></i>';
                                                for($i=0; $i<(5 - $rev['rating']); $i++) echo '<i class="fa-regular fa-star" style="color: rgba(255,255,255,0.15);"></i>';
                                                ?>
                                            </div>
                                        </div>
                                        <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.88rem; font-style: italic; margin: 0;">
                                            "<?= nl2br(htmlspecialchars($rev['comment'])) ?>"
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Star Rating Interactive Script -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const starLabels = document.querySelectorAll('.star-rating-widget label');
            const starInputs = document.querySelectorAll('.star-rating-widget input');
            
            starLabels.forEach((label, index) => {
                label.addEventListener('click', () => {
                    starInputs[index].checked = true;
                    
                    // Highlight selected stars and below
                    starLabels.forEach((lbl, idx) => {
                        if (idx <= index) {
                            lbl.style.color = '#eab308';
                            lbl.style.textShadow = '0 0 15px rgba(234, 179, 8, 0.6)';
                        } else {
                            lbl.style.color = 'rgba(255, 255, 255, 0.15)';
                            lbl.style.textShadow = 'none';
                        }
                    });
                });
                
                label.addEventListener('mouseenter', () => {
                    starLabels.forEach((lbl, idx) => {
                        if (idx <= index) {
                            lbl.style.color = '#facc15';
                            lbl.style.textShadow = '0 0 10px rgba(250, 204, 21, 0.4)';
                        }
                    });
                });
                
                label.addEventListener('mouseleave', () => {
                    let checkedIndex = -1;
                    starInputs.forEach((input, idx) => {
                        if (input.checked) {
                            checkedIndex = idx;
                        }
                    });
                    
                    starLabels.forEach((lbl, idx) => {
                        if (checkedIndex !== -1 && idx <= checkedIndex) {
                            lbl.style.color = '#eab308';
                            lbl.style.textShadow = '0 0 15px rgba(234, 179, 8, 0.6)';
                        } else {
                            lbl.style.color = 'rgba(255, 255, 255, 0.15)';
                            lbl.style.textShadow = 'none';
                        }
                    });
                });
            });

            // --- PREMIUM AUTOMATIC VERTICAL CAROUSEL TICKER ---
            const feed = document.querySelector('.reviews-feed-container');
            if (feed && feed.children.length > 2) {
                let intervalId;
                let isPaused = false;
                
                // Format layout for active auto-scrolling
                feed.style.maxHeight = '560px';
                feed.style.overflow = 'hidden';
                feed.style.position = 'relative';

                function startTicker() {
                    intervalId = setInterval(() => {
                        if (isPaused) return;
                        
                        const firstCard = feed.firstElementChild;
                        if (!firstCard) return;
                        
                        // Calculate height of the first card plus gap (16px)
                        const cardHeight = firstCard.offsetHeight + 16;
                        
                        // Ultra-smooth slide out
                        firstCard.style.transition = 'all 0.9s cubic-bezier(0.4, 0, 0.2, 1)';
                        firstCard.style.marginTop = `-${cardHeight}px`;
                        firstCard.style.opacity = '0';
                        firstCard.style.transform = 'scale(0.95) translateY(-10px)';
                        
                        // Move card to the bottom after transition finishes
                        setTimeout(() => {
                            firstCard.style.transition = 'none';
                            firstCard.style.marginTop = '0';
                            firstCard.style.opacity = '1';
                            firstCard.style.transform = 'none';
                            feed.appendChild(firstCard);
                        }, 900);
                    }, 4000); // Transitions every 4 seconds
                }
                
                startTicker();
                
                // Pause vertical scrolling when visitors hover to read comments comfortably
                feed.addEventListener('mouseenter', () => { isPaused = true; });
                feed.addEventListener('mouseleave', () => { isPaused = false; });
            }
        });
    </script>

    <!-- High-end reviews scrollbar CSS -->
    <style>
        .reviews-feed-wrapper {
            position: relative;
            max-height: 580px;
            overflow: hidden;
            border-radius: 16px;
            padding: 8px 0;
        }
        /* Top & Bottom elegant glassmorphic fade-out masks */
        .reviews-feed-wrapper::before,
        .reviews-feed-wrapper::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 60px;
            z-index: 10;
            pointer-events: none;
        }
        .reviews-feed-wrapper::before {
            top: 0;
            background: linear-gradient(to bottom, #0a0a0a 0%, rgba(10, 10, 10, 0) 100%);
        }
        .reviews-feed-wrapper::after {
            bottom: 0;
            background: linear-gradient(to top, #0a0a0a 0%, rgba(10, 10, 10, 0) 100%);
        }
        .reviews-feed-container {
            max-height: 560px;
            overflow-y: auto;
            padding: 8px 4px;
        }
        .reviews-feed-container::-webkit-scrollbar {
            width: 6px;
        }
        .reviews-feed-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 10px;
        }
        .reviews-feed-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            transition: all 0.2s;
        }
        .reviews-feed-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .review-bubble-card {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        .review-bubble-card:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.15) !important;
        }
    </style>

<?php include 'includes/footer.php'; ?>

<?php if($success_message): ?>
<script>
    Swal.fire({
        title: 'Review Submitted!',
        text: '<?= htmlspecialchars($success_message) ?>',
        icon: 'success',
        background: '#0a0a0a',
        color: '#fff',
        confirmButtonColor: '#fff',
        confirmButtonText: '<span style="color:#000;font-weight:bold;">Done</span>',
        customClass: {
            popup: 'border-secondary-custom'
        }
    });
</script>
<?php endif; ?>

<?php if($error_message): ?>
<script>
    Swal.fire({
        title: 'Oops...',
        text: '<?= htmlspecialchars($error_message) ?>',
        icon: 'error',
        background: '#0a0a0a',
        color: '#fff',
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Try Again'
    });
</script>
<?php endif; ?>

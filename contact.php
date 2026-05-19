<?php include 'includes/header.php'; ?>
<?php
$email = getSetting($pdo, 'email');
$phone = getSetting($pdo, 'phone');
$location = getSetting($pdo, 'location');

$success_message = false;
$error_message = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? 'send_message';
    
    if ($action == 'send_message') {
        $name = trim($_POST['name'] ?? '');
        $email_input = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        if (!empty($name) && !empty($email_input) && !empty($subject) && !empty($message)) {
            if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Please enter a valid, active email address.";
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $email_input, $subject, $message]);
                    $success_message = "Your message has been sent successfully! I will read it and get back to you soon.";
                    
                    // Construct Professional Auto-responder email
                    $emailSubject = "Thank you for reaching out! - Christian Rey M. Tangaro";
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
                                Thank you for reaching out! This is an automated confirmation to let you know that I have successfully received your inquiry through my personal portfolio contact terminal.
                            </div>
                            
                            <div class="message-box">
                                <div class="message-label">Your Submitted Inquiry</div>
                                <div style="margin-bottom: 12px;">
                                    <span style="color: #64748b; font-size: 12px;">Subject:</span>
                                    <span class="highlight" style="font-size: 13px;">' . htmlspecialchars($subject) . '</span>
                                </div>
                                <div class="message-label">Message</div>
                                <p class="message-content">' . nl2br(htmlspecialchars($message)) . '</p>
                            </div>
                            
                            <div class="greeting">
                                I am currently reviewing your request and will get back to you with a professional response as soon as possible (usually within 24 hours). If you need immediate assistance or would like to add further details, feel free to reply directly to this email.
                                <br><br>
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
                    
                    // Trigger Brevo SMTP API Email to User (Auto-responder)
                    sendBrevoEmail($email_input, $name, $emailSubject, $htmlBody);
                    
                    // Trigger Alert Email to Admin
                    $adminEmail = 'tangarochristianrey@gmail.com';
                    $adminSubject = "🚨 New Portfolio Message from " . htmlspecialchars($name);
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
                            .message-box { background-color: #181818; border: 1px solid #2a2a2a; border-radius: 12px; padding: 20px; margin: 20px 0; }
                            .message-label { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #64748b; margin-bottom: 6px; letter-spacing: 1px; }
                            .highlight { color: #ffffff; font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <div class="header">
                                <h1>New Message Notification</h1>
                                <p style="color: #64748b; font-size: 13px; margin: 5px 0 0 0;">Portfolio Inquiry Terminal</p>
                            </div>
                            <div class="divider"></div>
                            
                            <div style="font-size: 15px; color: #cbd5e1; line-height: 1.6;">
                                Hello Christian,<br><br>
                                You have received a new contact inquiry from your portfolio website. Here are the details:
                            </div>
                            
                            <div class="message-box">
                                <div class="message-label">Sender Details</div>
                                <div style="margin-bottom: 10px; font-size: 13px; color: #cbd5e1;">
                                    <strong>Name:</strong> <span class="highlight">' . htmlspecialchars($name) . '</span><br>
                                    <strong>Email:</strong> <span class="highlight">' . htmlspecialchars($email_input) . '</span>
                                </div>
                                <div class="divider" style="margin: 15px 0; border-style: dashed;"></div>
                                <div class="message-label">Message Details</div>
                                <div style="margin-bottom: 12px; font-size: 13px; color: #cbd5e1;">
                                    <strong>Subject:</strong> <span class="highlight">' . htmlspecialchars($subject) . '</span>
                                </div>
                                <p style="font-size: 13px; color: #cbd5e1; line-height: 1.5; margin: 0; white-space: pre-wrap;">' . nl2br(htmlspecialchars($message)) . '</p>
                            </div>
                            
                            <div style="text-align: center; margin-top: 30px;">
                                <a href="https://garu.rf.gd/admin/messages" target="_blank" style="background: #ffffff; color: #000000; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 13px; font-weight: bold; display: inline-block;">Manage Messages</a>
                            </div>
                        </div>
                    </body>
                    </html>
                    ';
                    sendBrevoEmail($adminEmail, 'Christian Rey M. Tangaro', $adminSubject, $adminBody);
                } catch (PDOException $e) {
                    $error_message = "Sorry, there was an issue sending your message. Please try again.";
                }
            }
        } else {
            $error_message = "Please fill in all required fields (Name, Email, Subject, and Message).";
        }
    }
}
?>

    <section id="contact" style="padding-top: 150px; min-height: 100vh; padding-bottom: 80px;">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="section-label">GET IN TOUCH</div>
                    <h2 class="section-title">Let's Talk</h2>
                    <p class="hero-desc mb-5">
                        I'm currently available to take on new projects, so feel free to send me a message about anything that you want to run past me.
                    </p>
                    
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex align-items-start gap-4 contact-row">
                            <div class="contact-item-icon mt-1"><i class="fa-solid fa-envelope fs-5"></i></div>
                            <div>
                                <h5 class="text-white mb-1 fs-6 fw-bold">Email</h5>
                                <p class="mb-0 fs-6" style="color: var(--text-secondary);"><?= htmlspecialchars($email) ?></p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-4 contact-row">
                            <div class="contact-item-icon mt-1"><i class="fa-solid fa-phone fs-5"></i></div>
                            <div>
                                <h5 class="text-white mb-1 fs-6 fw-bold">Phone</h5>
                                <p class="mb-0 fs-6" style="color: var(--text-secondary);"><?= htmlspecialchars($phone) ?></p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-4 contact-row">
                            <div class="contact-item-icon mt-1"><i class="fa-solid fa-location-dot fs-5"></i></div>
                            <div>
                                <h5 class="text-white mb-1 fs-6 fw-bold">Location</h5>
                                <p class="mb-0 fs-6" style="color: var(--text-secondary);"><?= htmlspecialchars_decode($location) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7">
                    <div class="info-card">
                        <form action="" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: var(--text-secondary); font-weight: 500;">Your Name</label>
                                        <input type="text" name="name" class="form-control" required placeholder="John Doe">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: var(--text-secondary); font-weight: 500;">Your Email</label>
                                        <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: var(--text-secondary); font-weight: 500;">Subject</label>
                                        <input type="text" name="subject" class="form-control" required placeholder="Project Proposal">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-4">
                                        <label class="form-label" style="color: var(--text-secondary); font-weight: 500;">Message</label>
                                        <textarea name="message" class="form-control" rows="5" required placeholder="Write your message here..."></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <input type="hidden" name="action" value="send_message">
                                    <button type="submit" class="btn-primary-custom w-100 py-3">Send Message <i class="fa-solid fa-paper-plane ms-2"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>

<?php if($success_message): ?>
<script>
    Swal.fire({
        title: 'Message Sent!',
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

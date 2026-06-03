<?php
include __DIR__ . '/mailer.php';

// Quick SMTP test page. Visit in browser: http://localhost/lost_found_system/test_email.php?to=you@example.com
$to = isset($_GET['to']) ? $_GET['to'] : '';

if (!$to) {
    echo '<form method="GET">';
    echo '<label>Send test email to:</label> <input name="to" type="email" required />';
    echo ' <button type="submit">Send</button>';
    echo '</form>';
    exit;
}

$subject = 'SMTP Test - Lost & Found System';
$html    = '<p>This is a test email from Lost & Found System.</p>';
$res     = send_email($to, $subject, $html);

if ($res['ok']) {
    echo '✅ Test email sent to ' . htmlspecialchars($to);
} else {
    echo '❌ Failed to send: ' . htmlspecialchars($res['error']);
}



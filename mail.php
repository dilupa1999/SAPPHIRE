<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

ob_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

$name    = strip_tags(trim($_POST['name'] ?? ''));
$email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone   = strip_tags(trim($_POST['phone'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

if (empty($name) || empty($phone) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Please fill in all fields correctly."]);
    exit;
}

// -------------------------------------------------------------
// 1. SMTP සැකසුම් (Settings)
// -------------------------------------------------------------
$smtpHost     = 'smtp.gmail.com';
$smtpUsername = 'lakshandilupa439@gmail.com';        // ඔබගේ Gmail ලිපිනය
$smtpPassword = 'pulddgiflhgtupwh';                  // ඔබගේ 16-digit App Password එක
$smtpPort     = 587;

// විස්තර ලැබිය යුතු නිල ඊමේල් ලිපිනය (Client Requirement)
$toEmail      = 'lakshandilupa439@gmail.com';             // Testing සඳහා අවශ්‍ය නම් ඔබේ email එක යොදන්න

// -------------------------------------------------------------
// 2. Brown Theme Modern Email Template (Buffer Style)
// -------------------------------------------------------------
$currentDate = date("F j, Y, g:i a");

$emailContent = "
<!DOCTYPE html>
<html lang='en'>
<head>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <title>Website Inquiry</title>
</head>
<body style='margin: 0; padding: 40px 15px; background-color: #f7f4f0; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;'>
  
  <table role='presentation' width='100%' border='0' cellspacing='0' cellpadding='0'>
    <tr>
      <td align='center'>
        
        <!-- Header Branding -->
        <table role='presentation' width='100%' style='max-width: 580px; margin-bottom: 24px;' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td align='center'>
              <h1 style='margin: 0; font-size: 23px; font-weight: 700; color: #4a2818; letter-spacing: 1px; text-transform: uppercase;'>
                Sapphire General Practice
              </h1>
              <p style='margin: 4px 0 0; font-size: 13px; color: #9c7a65; letter-spacing: 0.5px;'>
                Patient Care & Inquiries Portal
              </p>
            </td>
          </tr>
        </table>

        <!-- Main Content Card -->
        <table role='presentation' width='100%' style='max-width: 580px; background-color: #ffffff; border-radius: 14px; border: 1px solid #ebdcd0; box-shadow: 0 4px 18px rgba(74, 40, 24, 0.05); overflow: hidden;' border='0' cellspacing='0' cellpadding='0'>
          
          <!-- Top Accent Line -->
          <tr>
            <td style='height: 5px; background: linear-gradient(90deg, #7a4427, #b07d4b);'></td>
          </tr>

          <tr>
            <td style='padding: 38px 40px 32px;'>
              
              <h2 style='margin: 0 0 14px; font-size: 20px; font-weight: 600; color: #31190f;'>
                New Website Inquiry Received
              </h2>
              
              <p style='margin: 0 0 24px; font-size: 14.5px; line-height: 1.6; color: #5a4f47;'>
                You have received a new consultation inquiry from the clinic website contact form. The details provided by the patient are outlined below:
              </p>

              <!-- Patient Details Table Box -->
              <table role='presentation' width='100%' style='background-color: #fcfaf8; border-radius: 10px; border: 1px solid #f0e6dc; margin-bottom: 26px;' border='0' cellspacing='0' cellpadding='14'>
                <tr>
                  <td width='28%' style='font-size: 13px; font-weight: 700; color: #7a4427; text-transform: uppercase; border-bottom: 1px solid #f2e9e1; padding: 12px 16px;'>
                    Patient Name
                  </td>
                  <td style='font-size: 14.5px; font-weight: 600; color: #2e2621; border-bottom: 1px solid #f2e9e1; padding: 12px 16px;'>
                    {$name}
                  </td>
                </tr>
                <tr>
                  <td style='font-size: 13px; font-weight: 700; color: #7a4427; text-transform: uppercase; border-bottom: 1px solid #f2e9e1; padding: 12px 16px;'>
                    Email Address
                  </td>
                  <td style='font-size: 14px; color: #2e2621; border-bottom: 1px solid #f2e9e1; padding: 12px 16px;'>
                    <a href='mailto:{$email}' style='color: #8b4513; text-decoration: none; font-weight: 500;'>{$email}</a>
                  </td>
                </tr>
                <tr>
                  <td style='font-size: 13px; font-weight: 700; color: #7a4427; text-transform: uppercase; border-bottom: 1px solid #f2e9e1; padding: 12px 16px;'>
                    Phone Number
                  </td>
                  <td style='font-size: 14px; color: #2e2621; border-bottom: 1px solid #f2e9e1; padding: 12px 16px;'>
                    <a href='tel:{$phone}' style='color: #2e2621; text-decoration: none;'>{$phone}</a>
                  </td>
                </tr>
                <tr>
                  <td style='font-size: 13px; font-weight: 700; color: #7a4427; text-transform: uppercase; padding: 12px 16px;'>
                    Received On
                  </td>
                  <td style='font-size: 13.5px; color: #776e66; padding: 12px 16px;'>
                    {$currentDate}
                  </td>
                </tr>
              </table>

              <!-- Message Section -->
              <p style='margin: 0 0 8px; font-size: 13px; font-weight: 700; color: #7a4427; text-transform: uppercase; letter-spacing: 0.5px;'>
                Patient Message:
              </p>
              <div style='background-color: #f7f1eb; border-left: 4px solid #8b4513; border-radius: 0 8px 8px 0; padding: 16px 18px; margin-bottom: 30px;'>
                <p style='margin: 0; font-size: 14.5px; line-height: 1.65; color: #3d332d; font-style: normal;'>
                  " . nl2br($message) . "
                </p>
              </div>

              <!-- Action Call to Action Button (Buffer Inspired) -->
              <table role='presentation' width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                  <td align='center'>
                    <a href='mailto:{$email}?subject=RE: Sapphire GP Inquiry' target='_blank' style='display: inline-block; background-color: #6e391b; color: #ffffff; font-size: 14.5px; font-weight: 600; text-decoration: none; padding: 13px 34px; border-radius: 8px; box-shadow: 0 3px 10px rgba(110, 57, 27, 0.25); text-align: center;'>
                      Reply to Patient
                    </a>
                  </td>
                </tr>
              </table>

            </td>
          </tr>
        </table>

        <!-- Footer Note -->
        <table role='presentation' width='100%' style='max-width: 580px; margin-top: 24px;' border='0' cellspacing='0' cellpadding='0'>
          <tr>
            <td align='center' style='font-size: 12px; line-height: 1.6; color: #9c8e82;'>
              This email was automatically generated from the contact form on <br/>
              <strong style='color: #6e391b;'>Sapphire General Practice</strong> official website.<br/>
              All confidential information is handled securely.
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>

</body>
</html>
";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUsername;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $smtpPort;

    // SSL Verify bypass (Localhost XAMPP සඳහා)
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // Recipients
    $mail->setFrom($smtpUsername, 'Sapphire General Practice');
    $mail->addAddress($toEmail);
    $mail->addReplyTo($email, $name);

    // Email Layout & Content
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'New Website Inquiry: ' . $name;
    $mail->Body    = $emailContent;

    $mail->send();

    ob_clean();
    echo json_encode(["status" => "success", "message" => "Thank you! Your message has been sent successfully."]);
} catch (Exception $e) {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
}
exit;
?>
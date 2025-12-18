<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class PHPMailerService
{
    private $mailer = null;
    private LoggerInterface $logger;
    private string $smtpHost;
    private string $smtpUser;
    private string $smtpPassword;
    private int $smtpPort;
    private string $smtpEncryption;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        
        // Load SMTP configuration from environment (don't create PHPMailer yet)
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? 'localhost';
        $this->smtpUser = $_ENV['SMTP_USER'] ?? '';
        $this->smtpPassword = $_ENV['SMTP_PASSWORD'] ?? '';
        $this->smtpPort = (int)($_ENV['SMTP_PORT'] ?? 1025);
        $this->smtpEncryption = $_ENV['SMTP_ENCRYPTION'] ?? 'tls';
    }
    
    private function getMailer()
    {
        if ($this->mailer === null) {
            // Dynamically require the PHPMailer class files
            require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/Exception.php';
            require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
            require_once __DIR__ . '/../../vendor/phpmailer/phpmailer/src/SMTP.php';
            
            try {
                $className = '\\PHPMailer\\PHPMailer\\PHPMailer';
                $this->mailer = new $className(true);
                $this->configureMailer();
            } catch (\Exception $e) {
                $this->logger->error('Failed to initialize PHPMailer: ' . $e->getMessage());
                throw $e;
            }
        }
        return $this->mailer;
    }

    private function configureMailer(): void
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->smtpHost;
            $this->mailer->Port = $this->smtpPort;
            $this->mailer->SMTPSecure = $this->smtpEncryption === 'false' ? '' : $this->smtpEncryption;
            $this->mailer->SMTPAuth = !empty($this->smtpUser);
            
            if (!empty($this->smtpUser)) {
                $this->mailer->Username = $this->smtpUser;
                $this->mailer->Password = $this->smtpPassword;
            }
            
            // Set default sender
            $this->mailer->setFrom('noreply@backtogreen.com', 'BackToGreen');
            
            // Enable debugging for development
            if ($_ENV['APP_ENV'] === 'dev') {
                $this->mailer->SMTPDebug = 0; // Set to 2 for detailed debug output
            }
        } catch (\Exception $e) {
            $this->logger->error('PHPMailer configuration failed: ' . $e->getMessage());
        }
    }

    /**
     * Send an email
     *
     * @param string $toEmail Recipient email address
     * @param string $toName Recipient name
     * @param string $subject Email subject
     * @param string $htmlBody HTML email body
     * @param string|null $replyTo Optional reply-to email
     * @return bool True if email was sent successfully
     */
    public function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        ?string $replyTo = null
    ): bool
    {
        try {
            $mailer = $this->getMailer();
            
            // Clear previous recipients
            $mailer->clearAllRecipients();
            
            // Add recipient
            $mailer->addAddress($toEmail, $toName);
            
            // Add reply-to if provided
            if ($replyTo) {
                $mailer->addReplyTo($replyTo);
            }
            
            // Set email content
            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $htmlBody;
            $mailer->AltBody = strip_tags($htmlBody);
            
            // Send the email
            $mailer->send();
            
            $this->logger->info("Email sent successfully to $toEmail with subject: $subject");
            return true;
        } catch (\Exception $e) {
            $this->logger->error("Failed to send email to $toEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send an email to multiple recipients
     *
     * @param array $recipients Array of ['email' => 'name'] pairs
     * @param string $subject Email subject
     * @param string $htmlBody HTML email body
     * @return bool True if email was sent successfully
     */
    public function sendToMultiple(array $recipients, string $subject, string $htmlBody): bool
    {
        try {
            $mailer = $this->getMailer();
            
            $mailer->clearAllRecipients();
            
            foreach ($recipients as $email => $name) {
                $mailer->addAddress($email, $name);
            }
            
            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $htmlBody;
            $mailer->AltBody = strip_tags($htmlBody);
            
            $mailer->send();
            
            $this->logger->info("Email sent to " . count($recipients) . " recipients with subject: $subject");
            return true;
        } catch (\Exception $e) {
            $this->logger->error("Failed to send bulk email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Test SMTP connection
     *
     * @return bool True if connection is successful
     */
    public function testConnection(): bool
    {
        try {
            $mailer = $this->getMailer();
            $mailer->smtpConnect();
            $mailer->smtpClose();
            $this->logger->info("SMTP connection test successful");
            return true;
        } catch (\Exception $e) {
            $this->logger->error("SMTP connection test failed: " . $e->getMessage());
            return false;
        }
    }
}

# Mailtrap Configuration for WhatsML

## 🚀 Mailtrap Setup Guide

### What is Mailtrap?
Mailtrap is a safe email testing environment that captures all emails sent from your application without sending them to real users. Perfect for development and testing.

### Your Mailtrap Configuration
- **Password**: `d62bfacda4f02528aad2019527a2fb41`
- **Host**: `sandbox.smtp.mailtrap.io`
- **Port**: `2525`
- **Username**: `your_mailtrap_username` (you'll need to get this from Mailtrap dashboard)

## Getting Your Mailtrap Username

### Step 1: Access Mailtrap Dashboard
1. **Visit**: https://mailtrap.io
2. **Sign up** or **Login** to your account
3. **Go to**: Email Testing → Inboxes

### Step 2: Get SMTP Credentials
1. **Click** on your inbox
2. **Go to**: SMTP Settings tab
3. **Copy** the username (it will be something like `1a2b3c4d5e6f7g`)
4. **Password** is already provided: `d62bfacda4f02528aad2019527a2fb41`

## Configuration in WhatsML

### Environment Variables
```env
# Mailtrap Configuration (Testing)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username_here
MAIL_PASSWORD=d62bfacda4f02528aad2019527a2fb41
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="WhatsML Pro"
```

### Laravel Configuration
Update `config/mail.php`:
```php
'mailers' => [
    'smtp' => [
        'transport' => 'smtp',
        'host' => env('MAIL_HOST', 'sandbox.smtp.mailtrap.io'),
        'port' => env('MAIL_PORT', 2525),
        'encryption' => env('MAIL_ENCRYPTION', 'tls'),
        'username' => env('MAIL_USERNAME'),
        'password' => env('MAIL_PASSWORD'),
        'timeout' => null,
        'local_domain' => env('MAIL_EHLO_DOMAIN'),
    ],
],
```

## Testing Email Functionality

### Test Email Route
```php
Route::get('/test-email', function () {
    try {
        Mail::raw('Test email from WhatsML', function ($message) {
            $message->to('test@example.com')
                    ->subject('WhatsML Test Email');
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully! Check Mailtrap inbox.'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});
```

### Test Registration Email
```php
Route::post('/test-registration-email', function (Request $request) {
    $email = $request->input('email', 'test@example.com');
    
    try {
        Mail::to($email)->send(new WelcomeEmail([
            'name' => 'Test User',
            'email' => $email
        ]));
        
        return response()->json([
            'success' => true,
            'message' => 'Registration email sent! Check Mailtrap inbox.'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});
```

## Email Templates for WhatsML

### 1. Welcome Email
```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Welcome to WhatsML Pro!')
                    ->view('emails.welcome')
                    ->with(['user' => $this->user]);
    }
}
```

### 2. Password Reset Email
```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;

    public function __construct($token, $user)
    {
        $this->token = $token;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Reset Your Password - WhatsML Pro')
                    ->view('emails.password-reset')
                    ->with([
                        'token' => $this->token,
                        'user' => $this->user
                    ]);
    }
}
```

### 3. Subscription Confirmation Email
```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $user;

    public function __construct($subscription, $user)
    {
        $this->subscription = $subscription;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Subscription Confirmed - WhatsML Pro')
                    ->view('emails.subscription-confirmation')
                    ->with([
                        'subscription' => $this->subscription,
                        'user' => $this->user
                    ]);
    }
}
```

## Email Views

### Welcome Email Template
```html
<!-- resources/views/emails/welcome.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to WhatsML Pro</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
        <h1 style="color: #1daa61;">Welcome to WhatsML Pro!</h1>
        
        <p>Hello {{ $user['name'] }},</p>
        
        <p>Thank you for joining WhatsML Pro! We're excited to help you automate your WhatsApp business communications.</p>
        
        <h2>What's Next?</h2>
        <ul>
            <li>Set up your WhatsApp integration</li>
            <li>Create your first campaign</li>
            <li>Configure auto-replies</li>
            <li>Import your contacts</li>
        </ul>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/dashboard" 
               style="background-color: #1daa61; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">
                Go to Dashboard
            </a>
        </div>
        
        <p>If you have any questions, feel free to contact our support team.</p>
        
        <p>Best regards,<br>The WhatsML Pro Team</p>
    </div>
</body>
</html>
```

### Password Reset Template
```html
<!-- resources/views/emails/password-reset.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Your Password - WhatsML Pro</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
        <h1 style="color: #1daa61;">Reset Your Password</h1>
        
        <p>Hello {{ $user['name'] }},</p>
        
        <p>You requested to reset your password for your WhatsML Pro account.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/reset-password?token={{ $token }}" 
               style="background-color: #1daa61; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;">
                Reset Password
            </a>
        </div>
        
        <p>This link will expire in 60 minutes for security reasons.</p>
        
        <p>If you didn't request this password reset, please ignore this email.</p>
        
        <p>Best regards,<br>The WhatsML Pro Team</p>
    </div>
</body>
</html>
```

## Mailtrap Features

### 1. Email Testing
- **Safe Environment**: No emails sent to real users
- **Multiple Inboxes**: Organize emails by project
- **Email Preview**: See how emails look
- **HTML Analysis**: Check email structure

### 2. Email Analytics
- **Delivery Tracking**: Monitor email delivery
- **Open Rates**: Track email opens
- **Click Tracking**: Monitor link clicks
- **Spam Analysis**: Check spam score

### 3. Team Collaboration
- **Shared Inboxes**: Team access to emails
- **Comments**: Add notes to emails
- **Email Forwarding**: Forward emails to team members
- **Export Options**: Download emails as files

## Free Tier Limits

### Mailtrap Free Plan
- **100 emails/month**: Sufficient for testing
- **1 inbox**: Single project inbox
- **Email history**: 7 days retention
- **No cost**: Completely free

### When to Upgrade
- **More emails**: Exceed 100 emails/month
- **Multiple projects**: Need separate inboxes
- **Longer retention**: Need email history longer than 7 days
- **Team features**: Need shared access

## Production Email Setup

### For Production (SendGrid)
```env
# Production Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="WhatsML Pro"
```

### Environment-Based Configuration
```php
// In AppServiceProvider.php
public function boot()
{
    if (app()->environment('production')) {
        config([
            'mail.mailers.smtp.host' => 'smtp.sendgrid.net',
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.username' => 'apikey',
            'mail.mailers.smtp.password' => env('SENDGRID_API_KEY'),
        ]);
    } else {
        config([
            'mail.mailers.smtp.host' => 'sandbox.smtp.mailtrap.io',
            'mail.mailers.smtp.port' => 2525,
            'mail.mailers.smtp.username' => env('MAILTRAP_USERNAME'),
            'mail.mailers.smtp.password' => env('MAILTRAP_PASSWORD'),
        ]);
    }
}
```

## Testing Checklist

### Email Functionality Tests
- [ ] User registration email
- [ ] Password reset email
- [ ] Email verification
- [ ] Subscription confirmation
- [ ] Payment receipt
- [ ] Newsletter subscription
- [ ] Support ticket notifications
- [ ] WhatsApp message notifications

### Email Template Tests
- [ ] HTML rendering
- [ ] Mobile responsiveness
- [ ] Spam score check
- [ ] Link functionality
- [ ] Image loading
- [ ] Font rendering
- [ ] Color scheme
- [ ] Brand consistency

## Troubleshooting

### Common Issues
1. **Authentication Failed**: Check username and password
2. **Connection Timeout**: Verify host and port settings
3. **Emails Not Received**: Check Mailtrap inbox
4. **Template Errors**: Verify Blade template syntax

### Debug Commands
```bash
# Test SMTP connection
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });

# Check mail configuration
php artisan config:show mail

# Clear mail cache
php artisan config:clear
```

This Mailtrap configuration provides a safe testing environment for all email functionality in your WhatsML application, ensuring emails work correctly before going to production.

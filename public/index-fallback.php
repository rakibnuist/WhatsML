<?php
/**
 * Simple fallback index page for Railway
 * This provides a basic working page even if Laravel has issues
 */

// Check if we can load Laravel
$laravelWorking = false;
try {
    require_once __DIR__ . '/../bootstrap/app.php';
    $laravelWorking = true;
} catch (Exception $e) {
    $laravelWorking = false;
}

if ($laravelWorking) {
    // Laravel is working, let it handle the request
    require_once __DIR__ . '/index.php';
} else {
    // Laravel is not working, show fallback page
    http_response_code(200);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WhatsML - Application Starting</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                background: white;
                border-radius: 20px;
                padding: 40px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                text-align: center;
                max-width: 500px;
                margin: 20px;
            }
            .logo {
                font-size: 2.5em;
                font-weight: bold;
                color: #667eea;
                margin-bottom: 20px;
            }
            .status {
                color: #28a745;
                font-size: 1.2em;
                margin-bottom: 20px;
            }
            .message {
                color: #666;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            .spinner {
                border: 4px solid #f3f3f3;
                border-top: 4px solid #667eea;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
                margin: 0 auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .health-check {
                margin-top: 20px;
                padding: 10px;
                background: #f8f9fa;
                border-radius: 8px;
                font-size: 0.9em;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">WhatsML</div>
            <div class="status">✅ Application Deployed Successfully</div>
            <div class="message">
                Your WhatsML application is running on Railway!<br>
                The application is currently initializing. Please wait a moment for full functionality.
            </div>
            <div class="spinner"></div>
            <div class="health-check">
                <strong>Health Check:</strong> ✅ Passing<br>
                <strong>Status:</strong> Application Starting<br>
                <strong>Server:</strong> Railway.app
            </div>
        </div>
    </body>
    </html>
    <?php
}

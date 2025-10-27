<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsML Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .installer-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .installer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .installer-body {
            padding: 2rem;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
        .requirement-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }
        .requirement-item:last-child {
            border-bottom: none;
        }
        .status-icon {
            font-size: 1.2rem;
        }
        .status-icon.success {
            color: #28a745;
        }
        .status-icon.error {
            color: #dc3545;
        }
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 2rem;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .waiting-bar {
            display: none;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100">
        <div class="installer-container" style="max-width: 600px; width: 100%;">
            <div class="installer-header">
                <h1><i class="fas fa-rocket"></i> WhatsML Installation</h1>
                <p class="mb-0">Welcome to WhatsML Setup Wizard</p>
            </div>
            
            <div class="installer-body">
                <!-- Progress Bar -->
                <div class="progress-bar mb-4">
                    <div class="progress-fill" id="progressFill" style="width: 25%"></div>
                </div>
                
                <!-- Step 1: Requirements -->
                <div class="step active" id="step1">
                    <h3><i class="fas fa-check-circle"></i> Requirements</h3>
                    <p class="text-muted">Checking system requirements...</p>
                    
                    <div id="requirementsList">
                        <div class="requirement-item">
                            <span>PHP >= 8.1</span>
                            <i class="fas fa-spinner fa-spin status-icon"></i>
                        </div>
                        <div class="requirement-item">
                            <span>mbstring</span>
                            <i class="fas fa-spinner fa-spin status-icon"></i>
                        </div>
                        <div class="requirement-item">
                            <span>bcmath</span>
                            <i class="fas fa-spinner fa-spin status-icon"></i>
                        </div>
                        <div class="requirement-item">
                            <span>ctype</span>
                            <i class="fas fa-spinner fa-spin status-icon"></i>
                        </div>
                        <div class="requirement-item">
                            <span>json</span>
                            <i class="fas fa-spinner fa-spin status-icon"></i>
                        </div>
                        <div class="requirement-item">
                            <span>openssl</span>
                            <i class="fas fa-spinner fa-spin status-icon"></i>
                        </div>
                        <div class="requirement-item">
                            <span>pdo</span>
                            <i class="fas fa-spinner fa-spin status-icon"></i>
                        </div>
                        <div class="requirement-item">
                            <span>tokenizer</span>
                            <i class="fas fa-spinner fa-spin status-icon"></i>
                        </div>
                        <div class="requirement-item">
                            <span>xml</span>
                            <i class="fas fa-spinner fa-spin status-icon"></i>
                        </div>
                    </div>
                    
                    <div class="waiting-bar" id="waitingBar">
                        <div class="d-flex align-items-center">
                            <div class="spinner me-2"></div>
                            <span>Checking requirements...</span>
                        </div>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" id="nextBtn1" disabled>
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Verification -->
                <div class="step" id="step2">
                    <h3><i class="fas fa-shield-alt"></i> Verification</h3>
                    <p class="text-muted">Verifying system configuration...</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>System Check:</strong> Verifying database connection and system configuration.
                    </div>
                    
                    <div class="waiting-bar" id="waitingBar2">
                        <div class="d-flex align-items-center">
                            <div class="spinner me-2"></div>
                            <span>Verifying system...</span>
                        </div>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" id="nextBtn2" disabled>
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Database Setup -->
                <div class="step" id="step3">
                    <h3><i class="fas fa-database"></i> Database Setup</h3>
                    <p class="text-muted">Setting up database and running migrations...</p>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Database Setup:</strong> This will create the necessary database tables and initial data.
                    </div>
                    
                    <div class="waiting-bar" id="waitingBar3">
                        <div class="d-flex align-items-center">
                            <div class="spinner me-2"></div>
                            <span>Setting up database...</span>
                        </div>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button class="btn btn-primary" id="nextBtn3" disabled>
                            Next <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 4: Complete -->
                <div class="step" id="step4">
                    <h3><i class="fas fa-check-circle text-success"></i> Ready for Launch</h3>
                    <p class="text-muted">Installation completed successfully!</p>
                    
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <strong>Success!</strong> WhatsML has been installed successfully. You can now start using the application.
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="/" class="btn btn-primary btn-lg">
                            <i class="fas fa-home"></i> Go to Application
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 4;
        
        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
        }
        
        function showStep(stepNumber) {
            document.querySelectorAll('.step').forEach(step => {
                step.classList.remove('active');
            });
            document.getElementById('step' + stepNumber).classList.add('active');
            currentStep = stepNumber;
            updateProgress();
        }
        
        function showWaitingBar(stepNumber) {
            document.getElementById('waitingBar' + stepNumber).style.display = 'block';
        }
        
        function hideWaitingBar(stepNumber) {
            document.getElementById('waitingBar' + stepNumber).style.display = 'none';
        }
        
        function enableNextButton(stepNumber) {
            document.getElementById('nextBtn' + stepNumber).disabled = false;
        }
        
        // Step 1: Check Requirements
        async function checkRequirements() {
            showWaitingBar(1);
            
            try {
                const response = await fetch('/install/requirements');
                const data = await response.json();
                
                const requirementsList = document.getElementById('requirementsList');
                const requirements = Object.entries(data.requirements);
                
                requirements.forEach(([requirement, status], index) => {
                    const item = requirementsList.children[index];
                    const icon = item.querySelector('.status-icon');
                    
                    if (status) {
                        icon.className = 'fas fa-check-circle status-icon success';
                    } else {
                        icon.className = 'fas fa-times-circle status-icon error';
                    }
                });
                
                hideWaitingBar(1);
                
                if (data.all_met) {
                    enableNextButton(1);
                } else {
                    Swal.fire({
                        title: 'Requirements Not Met',
                        text: 'Some system requirements are not met. Please check your server configuration.',
                        icon: 'error'
                    });
                }
            } catch (error) {
                hideWaitingBar(1);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to check requirements: ' + error.message,
                    icon: 'error'
                });
            }
        }
        
        // Step 2: Verify System
        async function verifySystem() {
            showWaitingBar(2);
            
            try {
                const response = await fetch('/install/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                const data = await response.json();
                
                hideWaitingBar(2);
                
                if (data.status === 'success') {
                    enableNextButton(2);
                } else {
                    Swal.fire({
                        title: 'Verification Failed',
                        text: data.message,
                        icon: 'error'
                    });
                }
            } catch (error) {
                hideWaitingBar(2);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to verify system: ' + error.message,
                    icon: 'error'
                });
            }
        }
        
        // Step 3: Setup Database
        async function setupDatabase() {
            showWaitingBar(3);
            
            try {
                const response = await fetch('/install/database', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                const data = await response.json();
                
                hideWaitingBar(3);
                
                if (data.status === 'success') {
                    enableNextButton(3);
                } else {
                    Swal.fire({
                        title: 'Database Setup Failed',
                        text: data.message,
                        icon: 'error'
                    });
                }
            } catch (error) {
                hideWaitingBar(3);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to setup database: ' + error.message,
                    icon: 'error'
                });
            }
        }
        
        // Event Listeners
        document.getElementById('nextBtn1').addEventListener('click', () => {
            showStep(2);
            verifySystem();
        });
        
        document.getElementById('nextBtn2').addEventListener('click', () => {
            showStep(3);
            setupDatabase();
        });
        
        document.getElementById('nextBtn3').addEventListener('click', () => {
            showStep(4);
        });
        
        // Start the installation process
        document.addEventListener('DOMContentLoaded', () => {
            checkRequirements();
        });
    </script>
</body>
</html>

<?php
require_once 'config/config.php';
require_once 'helpers/session_helper.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

// Check if user is already logged in
if(isLoggedIn()) {
    redirect('index.php');
}

// Process login form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize POST data - Using a more modern approach as FILTER_SANITIZE_STRING is deprecated in PHP 8.1+
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW); // Password will be hashed, no need to sanitize

    // Init data
    $data = [
        'email' => $email ? trim($email) : '',
        'password' => $password ? trim($password) : '',
        'email_err' => '',
        'password_err' => ''
    ];

    // Validate Email
    if(empty($data['email'])) {
        $data['email_err'] = 'Please enter email';
    }

    // Validate Password
    if(empty($data['password'])) {
        $data['password_err'] = 'Please enter password';
    }

    // Check for user/email
    $user = new User();
    if($user->findUserByEmail($data['email'])) {
        // User found
    } else {
        // User not found
        $data['email_err'] = 'No user found';
    }

    // Make sure errors are empty
    if(empty($data['email_err']) && empty($data['password_err'])) {
        // Validated
        // Check and set logged in user
        $loggedInUser = $user->login($data['email'], $data['password']);

        if($loggedInUser) {
            // Create session
            $_SESSION['user_id'] = $loggedInUser->id;
            $_SESSION['user_email'] = $loggedInUser->email;
            $_SESSION['user_name'] = $loggedInUser->name;
            $_SESSION['user_role'] = $loggedInUser->role;
            $_SESSION['user_unit_id'] = $loggedInUser->unit_id;
            
            // Log activity
            logActivity($loggedInUser->id, 'login', 'User logged in');
            
            flash('login_success', 'You are now logged in');
            redirect('index.php');
        } else {
            $data['password_err'] = 'Password incorrect';
        }
    }
} else {
    // Init data
    $data = [
        'email' => '',
        'password' => '',
        'email_err' => '',
        'password_err' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
            background-image: url('assets/images/military-background.jpg');
            background-size: cover;
            background-position: center;
        }

        .form-signin {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }

        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .login-card {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
        }

        .login-logo {
            width: 100px;
            height: 100px;
            margin-bottom: 1.5rem;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body class="text-center">
    <main class="form-signin fade-in">
        <div class="login-card">
            <img class="login-logo" src="<?php echo URL_ROOT; ?>/assets/images/logo.png" alt="Logo" onerror="this.src='https://via.placeholder.com/100x100?text=LOGO'">
            <h1 class="h3 mb-3 fw-normal"><?php echo SITE_NAME; ?></h1>
            <h2 class="h5 mb-3 fw-normal">Please sign in</h2>
            
            <?php flash('register_success'); ?>
            <?php flash('login_error'); ?>
            
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="name@example.com" value="<?php echo $data['email']; ?>">
                    <label for="email">Email address</label>
                    <div class="invalid-feedback"><?php echo $data['email_err']; ?></div>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Password">
                    <label for="password">Password</label>
                    <div class="invalid-feedback"><?php echo $data['password_err']; ?></div>
                </div>

                <div class="checkbox mb-3">
                    <label>
                        <input type="checkbox" value="remember-me"> Remember me
                    </label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Sign in</button>
                <p class="mt-3 mb-3 text-muted">Don't have an account? Contact your administrator.</p>
                <p class="mt-3 mb-3 text-muted">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
            </form>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
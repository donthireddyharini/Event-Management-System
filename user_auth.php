<?php
if(!isset($_SESSION)) {
    session_start();
}
include 'admin/db_connect.php';

if(isset($_POST['action'])) {
    $action = $_POST['action'];

    if($action === 'user_login') {
        $email = $conn->real_escape_string($_POST['email']);
        $password = md5($_POST['password']);
        $qry = $conn->query("SELECT * FROM users WHERE username='$email' AND password='$password'");
        if($qry && $qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email']= $user['username'];
            echo json_encode(['status'=>1,'name'=>$user['name'],'email'=>$user['username']]);
        } else {
            echo json_encode(['status'=>0,'msg'=>'Invalid email or password.']);
        }
        exit;
    }

    if($action === 'user_signup') {
        $name    = $conn->real_escape_string($_POST['name']);
        $email   = $conn->real_escape_string($_POST['email']);
        $password= md5($_POST['password']);
        
        $chk = $conn->query("SELECT id FROM users WHERE username='$email'");
        if($chk && $chk->num_rows > 0) {
            echo json_encode(['status'=>2,'msg'=>'This email is already registered. Please sign in.']);
        } else {
            $ins = $conn->query("INSERT INTO users SET name='$name', username='$email', password='$password', type=3");
            if($ins) {
                $id = $conn->insert_id;
                $_SESSION['user_id']   = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email']= $email;
                echo json_encode(['status'=>1,'name'=>$name,'email'=>$email]);
            } else {
                echo json_encode(['status'=>0,'msg'=>'Registration failed: ' . $conn->error]);
            }
        }
        exit;
    }

    if($action === 'google_login') {
        $name  = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $qry = $conn->query("SELECT * FROM users WHERE username='$email'");
        if($qry && $qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email']= $user['username'];
        } else {
            $dummyPass = md5(uniqid(rand(), true));
            $conn->query("INSERT INTO users SET name='$name', username='$email', password='$dummyPass', type=3");
            $_SESSION['user_id']   = $conn->insert_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email']= $email;
        }
        echo json_encode(['status'=>1,'name'=>$name,'email'=>$email]);
        exit;
    }

    if($action === 'check_login') {
        if(isset($_SESSION['user_id'])) {
            echo json_encode(['status'=>1,'name'=>$_SESSION['user_name'],'email'=>$_SESSION['user_email']]);
        } else {
            echo json_encode(['status'=>0]);
        }
        exit;
    }

    if($action === 'user_logout') {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email']);
        echo json_encode(['status'=>1]);
        exit;
    }

    if($action === 'save_review') {
        $name    = $conn->real_escape_string($_POST['name']);
        $email   = $conn->real_escape_string($_POST['email']);
        $rating  = intval($_POST['rating']);
        $event_type = $conn->real_escape_string($_POST['event_type']);
        $comment = $conn->real_escape_string($_POST['comment']);

        $ins = $conn->query("INSERT INTO reviews (name, email, rating, event_type, comment) VALUES ('$name', '$email', $rating, '$event_type', '$comment')");
        if($ins) {
            echo json_encode(['status'=>1, 'msg'=>'Thank you! Your review has been submitted.']);
        } else {
            echo json_encode(['status'=>0, 'msg'=>'Error saving review: ' . $conn->error]);
        }
        exit;
    }
}
?>
<div class="container-fluid p-2" style="max-width:440px; margin:auto;">
    <!-- Hide the default Save button from uni_modal -->
    <style>
        #uni_modal .modal-footer {
            display: none !important;
        }
        .auth-tab-btn {
            font-weight: 600;
            border-radius: 20px !important;
            padding: 8px 20px;
            margin: 0 4px;
        }
        .auth-tab-btn.active {
            background-color: #f4623a !important;
            color: #fff !important;
            border-color: #f4623a !important;
        }
    </style>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills justify-content-center mb-3" id="authTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link auth-tab-btn active" id="tab-login-btn" data-toggle="pill" href="#loginPanel" role="tab">
                <i class="fa fa-sign-in-alt mr-1"></i> Sign In
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link auth-tab-btn" id="tab-signup-btn" data-toggle="pill" href="#signupPanel" role="tab">
                <i class="fa fa-user-plus mr-1"></i> Create Account
            </a>
        </li>
    </ul>

    <div class="tab-content pt-2">
        <!-- SIGN IN PANEL -->
        <div class="tab-pane fade show active" id="loginPanel" role="tabpanel">
            <!-- Google Sign-In Button Container -->
            <div class="text-center mb-3">
                <div id="google_login_btn" style="display:flex; justify-content:center; min-height:44px;"></div>
            </div>

            <div class="position-relative text-center my-3">
                <hr style="border-top: 1px solid #e0e0e0;">
                <span style="position: absolute; top: -11px; background: #fff; padding: 0 12px; color: #888; font-size: 13px; transform: translateX(-50%);">or with email</span>
            </div>

            <form id="authLoginForm">
                <div class="form-group mb-2">
                    <label class="small font-weight-bold mb-1">Email Address</label>
                    <input type="email" class="form-control" id="login_email_input" placeholder="name@example.com" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold mb-1">Password</label>
                    <input type="password" class="form-control" id="login_pass_input" placeholder="Your password" required>
                </div>
                <div id="login_error_alert" class="alert alert-danger py-1 px-2 small" style="display:none;"></div>
                <button type="submit" class="btn btn-primary btn-block font-weight-bold py-2" style="background-color: #f4623a; border-color: #f4623a;">
                    <i class="fa fa-sign-in-alt mr-1"></i> Sign In
                </button>
            </form>
        </div>

        <!-- SIGN UP PANEL -->
        <div class="tab-pane fade" id="signupPanel" role="tabpanel">
            <!-- Google Sign-Up Button Container -->
            <div class="text-center mb-3">
                <div id="google_signup_btn" style="display:flex; justify-content:center; min-height:44px;"></div>
            </div>

            <div class="position-relative text-center my-3">
                <hr style="border-top: 1px solid #e0e0e0;">
                <span style="position: absolute; top: -11px; background: #fff; padding: 0 12px; color: #888; font-size: 13px; transform: translateX(-50%);">or register with email</span>
            </div>

            <form id="authSignupForm">
                <div class="form-group mb-2">
                    <label class="small font-weight-bold mb-1">Full Name</label>
                    <input type="text" class="form-control" id="signup_name_input" placeholder="John Doe" required>
                </div>
                <div class="form-group mb-2">
                    <label class="small font-weight-bold mb-1">Email Address</label>
                    <input type="email" class="form-control" id="signup_email_input" placeholder="name@example.com" required>
                </div>
                <div class="form-group mb-3">
                    <label class="small font-weight-bold mb-1">Password</label>
                    <input type="password" class="form-control" id="signup_pass_input" placeholder="Create a password" required minlength="4">
                </div>
                <div id="signup_error_alert" class="alert alert-danger py-1 px-2 small" style="display:none;"></div>
                <button type="submit" class="btn btn-success btn-block font-weight-bold py-2">
                    <i class="fa fa-user-plus mr-1"></i> Create Account
                </button>
            </form>
        </div>
    </div>

    <!-- Back Button to return to website without signing in -->
    <div class="text-center mt-3 pt-2 border-top">
        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" data-dismiss="modal">
            <i class="fa fa-arrow-left mr-1"></i> Back to Website
        </button>
    </div>
</div>

<script>
// Hide modal-footer immediately
$('#uni_modal .modal-footer').hide();

// Trigger Google GSI Button render
if(typeof renderGoogleAuthButtons === 'function') {
    renderGoogleAuthButtons();
}

// Re-render when switching tabs
$('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
    if(typeof renderGoogleAuthButtons === 'function') {
        renderGoogleAuthButtons();
    }
});

// Submit Login Form
$('#authLoginForm').submit(function(e){
    e.preventDefault();
    $('#login_error_alert').hide();
    start_load();
    $.post('user_auth.php', {
        action: 'user_login',
        email: $('#login_email_input').val(),
        password: $('#login_pass_input').val()
    }, function(resp){
        end_load();
        try {
            var r = JSON.parse(resp);
            if(r.status == 1){
                onUserLoggedIn(r);
            } else {
                $('#login_error_alert').html(r.msg || 'Invalid credentials').fadeIn();
            }
        } catch(err) {
            $('#login_error_alert').html('Error processing request').fadeIn();
        }
    });
});

// Submit Signup Form
$('#authSignupForm').submit(function(e){
    e.preventDefault();
    $('#signup_error_alert').hide();
    start_load();
    $.post('user_auth.php', {
        action: 'user_signup',
        name: $('#signup_name_input').val(),
        email: $('#signup_email_input').val(),
        password: $('#signup_pass_input').val()
    }, function(resp){
        end_load();
        try {
            var r = JSON.parse(resp);
            if(r.status == 1){
                onUserLoggedIn(r);
            } else {
                $('#signup_error_alert').html(r.msg || 'Registration failed').fadeIn();
            }
        } catch(err) {
            $('#signup_error_alert').html('Error processing request').fadeIn();
        }
    });
});
</script>
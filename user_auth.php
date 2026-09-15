<?php
session_start();
include 'admin/db_connect.php';

if(isset($_POST['action'])) {
    $action = $_POST['action'];

    if($action === 'user_login') {
        $email = $conn->real_escape_string($_POST['email']);
        $password = md5($_POST['password']);
        $qry = $conn->query("SELECT * FROM users WHERE username='$email' AND password='$password' AND type=3");
        if($qry->num_rows > 0) {
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
        $contact = $conn->real_escape_string(isset($_POST['contact']) ? $_POST['contact'] : '');
        $chk = $conn->query("SELECT id FROM users WHERE username='$email'")->num_rows;
        if($chk > 0) {
            echo json_encode(['status'=>2,'msg'=>'Email already registered. Please login.']);
        } else {
            $conn->query("INSERT INTO users SET name='$name', username='$email', password='$password', contact='$contact', type=3");
            $id = $conn->insert_id;
            $_SESSION['user_id']   = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email']= $email;
            echo json_encode(['status'=>1,'name'=>$name,'email'=>$email]);
        }
        exit;
    }

    if($action === 'google_login') {
        $name  = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $qry = $conn->query("SELECT * FROM users WHERE username='$email' AND type=3");
        if($qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email']= $user['username'];
        } else {
            $conn->query("INSERT INTO users SET name='$name', username='$email', password='".md5(uniqid())."', type=3");
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
}
?>
<div class="container-fluid" style="max-width:420px;margin:auto;">
    <ul class="nav nav-tabs nav-justified mb-3" id="authTabs">
        <li class="nav-item"><a class="nav-link active" href="#loginTab" data-toggle="tab"><i class="fa fa-sign-in-alt"></i> Login</a></li>
        <li class="nav-item"><a class="nav-link" href="#signupTab" data-toggle="tab"><i class="fa fa-user-plus"></i> Sign Up</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="loginTab">
            <div class="text-center mb-2"><div id="google_login_btn" style="display:flex;justify-content:center;min-height:44px;"></div></div>
            <div class="text-center text-muted mb-3"><small>— or login with email —</small></div>
            <form id="loginForm">
                <div class="form-group"><label>Email</label><input type="email" class="form-control" id="login_email" placeholder="you@example.com" required></div>
                <div class="form-group"><label>Password</label><input type="password" class="form-control" id="login_password" placeholder="Password" required></div>
                <div id="login_msg" class="text-danger small mb-2"></div>
                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-sign-in-alt"></i> Login</button>
            </form>
        </div>
        <div class="tab-pane fade" id="signupTab">
            <div class="text-center mb-2"><div id="google_signup_btn" style="display:flex;justify-content:center;min-height:44px;"></div></div>
            <div class="text-center text-muted mb-3"><small>— or sign up with email —</small></div>
            <form id="signupForm">
                <div class="form-group"><label>Full Name</label><input type="text" class="form-control" id="su_name" placeholder="Your full name" required></div>
                <div class="form-group"><label>Email</label><input type="email" class="form-control" id="su_email" placeholder="you@example.com" required></div>
                <div class="form-group"><label>Password</label><input type="password" class="form-control" id="su_password" placeholder="Create a password" required></div>
                <div class="form-group"><label>Contact #</label><input type="text" class="form-control" id="su_contact" placeholder="+91 XXXXXXXXXX"></div>
                <div id="signup_msg" class="text-danger small mb-2"></div>
                <button type="submit" class="btn btn-success btn-block"><i class="fa fa-user-plus"></i> Create Account</button>
            </form>
        </div>
    </div>
</div>
<script>
(function renderGoogleBtns() {
    ['google_login_btn','google_signup_btn'].forEach(function(cid){
        var el = document.getElementById(cid);
        if(el && typeof google !== 'undefined' && google.accounts){
            el.innerHTML = '';
            google.accounts.id.initialize({client_id:'403671615206-8glqf2te5i5e04eqh1s5s1rtruhflq7s.apps.googleusercontent.com',callback:handleGoogleAuth});
            google.accounts.id.renderButton(el,{type:'standard',shape:'rectangular',theme:'outline',text:'signin_with',size:'large',logo_alignment:'left',width:300});
        }
    });
})();
$('#loginForm').submit(function(e){
    e.preventDefault(); start_load();
    $.post('user_auth.php',{action:'user_login',email:$('#login_email').val(),password:$('#login_password').val()},function(resp){
        var r=JSON.parse(resp); end_load();
        if(r.status==1){onUserLoggedIn(r);}else{$('#login_msg').html(r.msg);}
    });
});
$('#signupForm').submit(function(e){
    e.preventDefault(); start_load();
    $.post('user_auth.php',{action:'user_signup',name:$('#su_name').val(),email:$('#su_email').val(),password:$('#su_password').val(),contact:$('#su_contact').val()},function(resp){
        var r=JSON.parse(resp); end_load();
        if(r.status==1){onUserLoggedIn(r);}else{$('#signup_msg').html(r.msg);}
    });
});
</script>

 		<meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title><?php echo $_SESSION['system']['name'] ?></title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.13.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic" rel="stylesheet" type="text/css" />
        <!-- Third party plugin CSS-->
        <link href="admin/assets/css/jquery.datetimepicker.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="admin/assets/vendor/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
         <link href="admin/assets/css/select2.min.css" rel="stylesheet">

        <script src="admin/assets/vendor/jquery/jquery.min.js"></script>
        <script src="admin/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

    <script type="text/javascript" src="admin/assets/js/select2.min.js"></script>

    <script type="text/javascript" src="admin/assets/js/jquery.datetimepicker.full.min.js"></script>

        <!-- Google Identity Services — loaded early so it's ready when modal opens -->
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <script>
        // Pending action after login (e.g. open booking or registration)
        var _pendingAction = null;

        // Called by Google GSI after user picks account
        function handleGoogleAuth(response) {
            try {
                var base64Url = response.credential.split('.')[1];
                var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c){
                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                }).join(''));
                var userData = JSON.parse(jsonPayload);
                // Send to server to create/fetch session
                $.post('user_auth.php', {action:'google_login', name:userData.name, email:userData.email}, function(resp){
                    var r = JSON.parse(resp);
                    if(r.status == 1){ onUserLoggedIn(r); }
                });
            } catch(e){ console.error('Google auth error:', e); }
        }

        // Also used for registration form field fill (backward compat)
        function handleGoogleSignIn(response) { handleGoogleAuth(response); }

        // Called after any successful login (Google or email)
        function onUserLoggedIn(user) {
            $('#uni_modal').modal('hide');
            // Update navbar to show logged-in user
            updateUserNav(user.name, user.email);
            if(typeof alert_toast === 'function')
                alert_toast('Welcome, ' + user.name + '!', 'success');
            // Execute pending action (book/register)
            if(_pendingAction) {
                setTimeout(function(){ _pendingAction(); _pendingAction = null; }, 400);
            }
        }

        // Update nav to show logged-in state
        function updateUserNav(name, email) {
            var $btn = $('#user_nav_btn');
            if($btn.length) {
                $btn.html('<i class="fa fa-user"></i> ' + name)
                    .removeClass('btn-outline-light')
                    .addClass('btn-light');
            }
        }

        // Check if user session active, then run action or show login
        function requireLogin(action) {
            $.post('user_auth.php', {action:'check_login'}, function(resp){
                var r = JSON.parse(resp);
                if(r.status == 1) {
                    action(); // already logged in
                } else {
                    _pendingAction = action;
                    uni_modal('Sign In to Continue', 'user_auth.php');
                }
            });
        }

        // Re-render Google buttons whenever the auth modal opens
        $(document).on('shown.bs.modal', '#uni_modal', function() {
            ['google_login_btn','google_signup_btn','google_signin_btn'].forEach(function(cid){
                var el = document.getElementById(cid);
                if(el && typeof google !== 'undefined' && google.accounts){
                    el.innerHTML = '';
                    google.accounts.id.initialize({
                        client_id:'403671615206-8glqf2te5i5e04eqh1s5s1rtruhflq7s.apps.googleusercontent.com',
                        callback: handleGoogleAuth
                    });
                    google.accounts.id.renderButton(el,{
                        type:'standard', shape:'rectangular', theme:'outline',
                        text:'signin_with', size:'large', logo_alignment:'left', width:300
                    });
                }
            });
        });
        </script>



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

        <!-- Google Identity Services -->
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <script>
        var _pendingAction = null;

        function handleGoogleAuth(response) {
            try {
                var base64Url = response.credential.split('.')[1];
                var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c){
                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                }).join(''));
                var userData = JSON.parse(jsonPayload);
                start_load();
                $.post('user_auth.php', {action:'google_login', name:userData.name, email:userData.email}, function(resp){
                    end_load();
                    try {
                        var r = JSON.parse(resp);
                        if(r.status == 1){ 
                            onUserLoggedIn(r); 
                        } else {
                            if(typeof alert_toast === 'function') alert_toast('Google login failed', 'danger');
                        }
                    } catch(e) {
                        console.error('Parse error:', e);
                    }
                });
            } catch(e){ 
                console.error('Google auth decode error:', e); 
            }
        }

        function handleGoogleSignIn(response) { 
            handleGoogleAuth(response); 
        }

        function onUserLoggedIn(user) {
            updateUserNav(user.name, user.email);
            if(typeof alert_toast === 'function') {
                alert_toast('Welcome, ' + user.name + '!', 'success');
            }
            if(_pendingAction) {
                var action = _pendingAction;
                _pendingAction = null;
                $('#uni_modal').one('hidden.bs.modal', function(){
                    setTimeout(function(){ action(); }, 150);
                });
                $('#uni_modal').modal('hide');
            } else {
                $('#uni_modal').modal('hide');
            }
        }

        function updateUserNav(name, email) {
            var $container = $('#nav_user_container');
            if($container.length) {
                $container.html(
                    '<li class="nav-item dropdown">' +
                    '<a class="nav-link dropdown-toggle text-white" href="#" id="userNavDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                    '<i class="fa fa-user-circle mr-1"></i> ' + $('<div>').text(name).html() +
                    '</a>' +
                    '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userNavDropdown">' +
                    '<span class="dropdown-item-text small text-muted">' + $('<div>').text(email).html() + '</span>' +
                    '<div class="dropdown-divider"></div>' +
                    '<a class="dropdown-item" href="index.php?page=my_bookings"><i class="fa fa-history mr-1"></i> My Bookings</a>' +
                    '<div class="dropdown-divider"></div>' +
                    '<a class="dropdown-item" href="javascript:void(0)" onclick="userLogout()"><i class="fa fa-sign-out-alt mr-1"></i> Logout</a>' +
                    '</div>' +
                    '</li>'
                );
            }
        }

        function userLogout() {
            start_load();
            $.post('user_auth.php', {action:'user_logout'}, function(){
                end_load();
                location.reload();
            });
        }

        function requireLogin(action) {
            start_load();
            $.post('user_auth.php', {action:'check_login'}, function(resp){
                end_load();
                try {
                    var r = JSON.parse(resp);
                    if(r.status == 1) {
                        action();
                    } else {
                        _pendingAction = action;
                        uni_modal('Sign In to Continue', 'user_auth.php');
                    }
                } catch(e) {
                    _pendingAction = action;
                    uni_modal('Sign In to Continue', 'user_auth.php');
                }
            });
        }

        function renderGoogleAuthButtons() {
            var btnIds = ['google_login_btn','google_signup_btn','google_signin_btn'];
            btnIds.forEach(function(cid){
                var el = document.getElementById(cid);
                if(el){
                    if(typeof google !== 'undefined' && google.accounts && google.accounts.id){
                        el.innerHTML = '';
                        google.accounts.id.initialize({
                            client_id:'403671615206-8glqf2te5i5e04eqh1s5s1rtruhflq7s.apps.googleusercontent.com',
                            callback: handleGoogleAuth
                        });
                        var btnW = el.offsetWidth > 150 ? el.offsetWidth : 280;
                        google.accounts.id.renderButton(el,{
                            type:'standard', shape:'rectangular', theme:'outline',
                            text:'signin_with', size:'large', logo_alignment:'left', width: btnW
                        });
                    } else {
                        setTimeout(renderGoogleAuthButtons, 300);
                    }
                }
            });
        }

        $(document).on('shown.bs.modal', '#uni_modal', function() {
            renderGoogleAuthButtons();
        });
        </script>
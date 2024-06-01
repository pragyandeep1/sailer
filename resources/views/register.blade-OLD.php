<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

   <title>Busfam Admin - Register</title>


    <!-- Custom styles for this template-->
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/sb-admin.css">

</head>

<body class="intro-bg">
    <div class="auth-container">
        <div class="card">
            
            <div class="auth-content">
                <div class="auth-header">
                    <img src="img/login_logo.webp" alt="" class="img-fluid">
                </div>
                <h5>Sign Up</h5>
                
                
                <form method="post" action="#" id="introForm" class="formular">
                    <div class="mb-3">
                        <input type="text" class="validate[required] text-input form-control" placeholder="Enter your email ID" name="email"
                            id="email">
                    </div>
					<div class="mb-3">
                        <!-- <label for="username" class="form-label">Username</label> -->
                        <input type="text" class="validate[required] text-input form-control" placeholder="Username" name="user_name"
                            id="user_name">
                    </div>
					
                    <div class="mb-3">
                        <!-- <label for="password" class="form-label">Password</label> -->
                        <input type="password" class="form-control validate[required] text-input login-field  login-field-password" placeholder="Password"
                            id="password-1" name="password">
                    </div>
                   
                    <div class="mb-3 text-center"> <a href="index.php" type="submit" class="btn btn-block btn-primary">Sign Up</a> </div>
					<div class="mb-3 text-center"> 
					Already a User? <a href="login.php">Login</a> 
					</div>
					
                </form>
            </div>
        </div>
    </div>




    <!-- Bootstrap core JavaScript-->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.js"></script>
    <script src="js/hideShowPassword.min.js"></script>
    <script>
        $('#password-1').hidePassword(true);
        $('#password-2').showPassword('focus', {
            toggle: { className: 'my-toggle' }
        });
        $('#show-password').change(function () {
            $('#password-3').hideShowPassword($(this).prop('checked'));
        });
        jQuery(document).ready(function () {
            jQuery("#introForm").validationEngine('attach', { promptPosition: "bottomLeft", autoPositionUpdate: true });
        });
    </script>

</body>

</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <title>Laravel</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">

</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="/">
                    YAF
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    <li><a href="/login">Login</a></li>
                    <li><a href="/register">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Register</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="/register">
                            <?php csrf_field() ?>

                            <div class="form-group<?php echo(isset($errors['name']) ? ' has-error' : ''); ?>">
                                <label for="name" class="col-md-4 control-label">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="" required
                                           autofocus>
                                    <?php
                                    if (isset($errors['name']))
                                    {
                                        echo '<span class="help-block"><strong>' . $errors['name'] . '</strong></span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group<?php echo(isset($errors['phone']) ? ' has-error' : ''); ?>">
                                <label for="name" class="col-md-4 control-label">Phone</label>

                                <div class="col-md-6">
                                    <input id="phone" type="text" class="form-control" name="phone" value="" required
                                           autofocus>
                                    <?php
                                    if (isset($errors['phone']))
                                    {
                                        echo '<span class="help-block"><strong>' . $errors['phone'] . '</strong></span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group<?php echo(isset($errors['email']) ? ' has-error' : ''); ?>">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="" required>
                                    <?php
                                    if (isset($errors['email']))
                                    {
                                        echo '<span class="help-block"><strong>' . $errors['email'] . '</strong></span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group<?php echo(isset($errors['password']) ? ' has-error' : ''); ?>">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>
                                    <?php
                                    if (isset($errors['password']))
                                    {
                                        echo '<span class="help-block"><strong>' . $errors['password'] . '</strong></span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group<?php echo(isset($errors['password_confirmation']) ? ' has-error' : ''); ?>">
                                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                           name="password_confirmation" required>
                                    <?php
                                    if (isset($errors['password_confirmation']))
                                    {
                                        echo '<span class="help-block"><strong>' . $errors['password_confirmation'] . '</strong></span>';
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Register
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

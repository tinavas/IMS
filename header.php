<?php
/**
 * Created by PhpStorm.
 * User: lommi
 * Date: 23/01/2017
 * Time: 08:16 PM
 */
// Require init.php for core config and class auto load
require_once 'core/init.php';

$user = new user();

if ($user->isLoggedIn()) {

    //extra layer of access checking in case user is enabled but still not allowed access
    if ($user->hasPermission('disabled')) {

        $user->logout();
        $hash = new hash();
        redirect::to('index.php?' . hash::sha256('disabled' . $hash->getSalt()));

    }

    $name = escape($user->data()->name);
    $surname = escape($user->data()->surname);
    $uid = escape($user->data()->uid);

    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
        <title>IMS</title>
        <link rel='stylesheet prefetch'
              href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css'>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script>
            $(function () {
                $("#dialogOk").dialog({
                    modal: true,
                    buttons: {
                        Ok: function () {
                            $(this).dialog("close");
                        }
                    }
                });
            });
        </script>
        <script>
            $( function() {

                $( "#form-dialog" ).dialog({height:'auto',width:'auto'});
            } );
        </script>
    </head>
    <body>
    <div class="width-height">
        <div class="fixheader">
            <div class="header">
                <div class="logo">
                    <?php
                    if ($user->hasPermission('access') || $user->hasPermission('allAccess')) {
                        echo '<h1 class="header-heading"><a href="main.php"><strong>IMS</strong>HUB</a></h1>';
                    }
                    ?>
                </div>
                <div class="menu">
                    <ul class="nav">
                        <?php
                        if ($user->hasPermission('inventory') || $user->hasPermission('allAccess')) {
                            echo '<li><a href="inventory.php">Inventory</a></li>';
                        }
                        if ($user->hasPermission('allStockLevels') || $user->hasPermission('allAccess')) {
                            echo '<li><a href="stockLevels.php">Stock</a></li>';
                        }
                        if ($user->hasPermission('newCustomer') || $user->hasPermission('allAccess')) {
                            echo '<li><a href="newCustomer.php">New Customer</a></li>';
                        }
                        if ($user->hasPermission('reports') || $user->hasPermission('allAccess')) {
                            echo '<li><a href="reports.php">Reports</a></li>';
                        }
                        if ($user->hasPermission('admin') || $user->hasPermission('allAccess')) {
                            echo '<li><a href="admin.php">Admin</a></li>';
                        }
                        if ($user->hasPermission('access') || $user->hasPermission('allAccess')) {
                            echo '<br><div  class="user">
                                    <a href="profile.php">' . $name . ' ' . $surname . '</a>
                                    | 
                                    <a href="logout.php">Logout</a>
                                  </div>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="nav-bar">
                <div class="search">
                    <?php
                    if ($user->hasPermission('search') || $user->hasPermission('allAccess')) {
                    ?>
                        <form action="search.php" method="post" class="form-wrapper cf">
                            <input type="text" name="search" placeholder="Search here..." autocomplete="off" required="required">
                            <button type="submit">Search</button>
                        </form>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    $hash = new hash();
    redirect::to('index.php?' . hash::sha256('nologin' . $hash->getSalt()));
}
?>
<?php
require_once 'core/init.php';
include_once("header.php");

if ($user->isLoggedIn()) {

    //check if user has permission
    if ($user->hasPermission('changeUserStatus') || $user->hasPermission('allAccess')) {
        if (input::exists()) {

            if (token::check(input::get('token'))) {

                $validate = new validate();
                $validation = $validate->check($_POST, array(
                    'user' => array('required' => true),
                    'type' => array('required' => true)
                ));

                if ($validation->passed()) {

                    try {

                        $u = escape(input::get('user'));
                        $t = escape(input::get('type'));

                        $update = db::getInstance()->query("update ims.users set user_type_id = {$t} where uid = {$u}");

                        //get data to display dialog
                        $get = db::getInstance()->query("select concat(u.name, ' ', u.surname) name, ut.user_type from ims.users u inner join ims.user_types ut on u.user_type_id = ut.user_type_id where u.uid = {$u}");

                        //display dialog
                        if (!$get->count()) {

                            echo 'not ok';

                        } else {

                            //create message to display on user creation
                            ?>
                            <div id="dialogOk" title="Success">
                            <p>
                            <?php
                            foreach ($get->results() as $data) {
                                echo '<b>' . $data->name . '</b> has been updated to <b>' . $data->user_type . '</b>.';
                                ?>
                                </p>
                                </div>
                                <?php
                            }
                        }

                    } catch (Exception $e) {
                        die($e->getMessage());
                    }

                } else {
                    ?>
                    <div id="dialogOk" title="Error">
                        <?php
                        foreach ($validation->errors() as $error) {
                            echo "&#x26a0; " . $error . "", '<br>';
                        }
                        ?>
                    </div>
                    <?php
                }
            }
        }
        ?>
        <div class="content">
            <div class="container">
                <div class="form-style">
                    <h1>Change User Type</h1>
                    <form action="" method="post">
                        <select name="user">
                            <option value="">----------------------- Choose a User -----------------------</option>
                            <?php

                            $get = db::getInstance()->query("select uid, username from users where uid <> {$uid} order by uid");

                            if (!$get->count()) {
                                echo 'Empty List';
                            } else {

                                foreach ($get->results() as $u): ?>
                                    <option value="<?php echo escape($u->uid); ?>">
                                        <?php echo escape($u->username); ?>
                                    </option>
                                <?php endforeach;

                            }
                            ?>
                        </select>
                        <select name="type">
                            <option value="">---------------------- Choose a Type ----------------------</option>
                            <?php
                            $get = db::getInstance()->query('SELECT user_type_id, user_type FROM ims.user_types where user_type_id <> 9 order by 1');

                            if (!$get->count()) {
                                echo 'Empty List';
                            } else {

                                foreach ($get->results() as $t): ?>
                                    <option value="<?php echo escape($t->user_type_id); ?>">
                                        <?php echo escape($t->user_type); ?>
                                    </option>
                                <?php endforeach;

                            }
                            ?>
                        </select>
                        <input type="hidden" name="token" value="<?php echo token::generate(); ?>">
                        <input type="submit" value="UPDATE"/>
                    </form>
                </div>
            </div>
        </div>
        <?php
    } else {
        redirect::to('main.php');
    }

} else {
    $hash = new hash();
    redirect::to('index.php?' . hash::sha256('nologin' . $hash->getSalt()));
}
<?php
require_once 'core/init.php';
include_once ("header.php");

//$user = new user();

if ($user->isLoggedIn()){

    //check if user has permission
    if ($user->hasPermission('addResourceBrand') || $user->hasPermission('allAccess')){
        //Validate user input
        if(input::exists()){

            // validate whether token exists before performing any action
            if(token::check(input::get('token'))){

                // if token validation passed continue
                $validate = new validate();
                $validation = $validate->check($_POST,array(
                    'resource_brand' => array(
                        'required' => true,
                        'min' => 2,
                        'max' => 50,
                        'unique' => 'resource_brands'
                    )
                ));

                // display error or success messages
                if ($validation->passed()){

                    $inventory = new inventory();

                    $brand = escape(input::get('resource_brand'));

                    try {

                        $inventory->createResourceBrand(array('resource_brand' => $brand));

                        //create message to display on user creation
                        ?>
                        <div id="dialogOk" title="Success">
                            <p>
                                <?php
                                echo '<b>' . $brand . '</b> added successfully.';
                                ?>
                            </p>
                        </div>
                        <?php
                    } catch (Exception $e){
                        die($e->getMessage());
                    }
                } else {
                    ?>
                    <div id="dialogOk" title="Error">
                        <?php
                        foreach ($validation->errors() as $error){
                            echo '&#x26a0; ' . $error . "", '<br>';
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
                    <h1>Add a New Resource Brand</h1>

                    <form action="" method="post" name="addResourceBrand">
                        <input type="text" name="resource_brand" placeholder="New Brand Name" autocomplete="off" value="<?php echo escape(input::get('resource_brand')); ?>" />
                        <input type="hidden" name="token" value="<?php echo token::generate(); ?>">
                        <input type="submit" value="ADD RESOURCE BRAND"/>
                    </form>
                    <br>
                    <div class="form-link">
                        <a href="addResourceBrand.php">Clear Form</a>
                    </div>
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
<?php
require_once 'core/init.php';
include_once ("header.php");

//$user = new user();

if ($user->isLoggedIn()){

    //check if user has permission
    if ($user->hasPermission('addResourceModel') || $user->hasPermission('allAccess')){
        //Validate user input
        if(input::exists()){

            // validate whether token exists before performing any action
            if(token::check(input::get('token'))){

                // if token validation passed continue
                $validate = new validate();
                $validation = $validate->check($_POST,array(
                    'resource_model' => array(
                        'required' => true,
                        'min' => 5,
                        'max' => 50,
                        'unique' => 'resource_models'
                    ),
                    'resource_brand_id' => array('required' => true),
                    'resource_type_id' => array('required' => true)
                ));

                // display error or success messages
                if ($validation->passed()){

                    $inventory = new inventory();

                    $model = escape(input::get('resource_model'));
                    $brand = escape(input::get('resource_brand_id'));
                    $type = escape(input::get('resource_type_id'));

                    try {

                        $inventory->createResourceModel(array(
                            'resource_model' => $model,
                            'resource_brand_id' => $brand,
                            'resource_type_id' => $type
                        ));

                        //get data to display dialog
                        $get = db::getInstance()->query("select resource_brand from resource_brands where resource_brand_id = {$brand}");

                        if (!$get->count()){
                            echo 'not ok';
                        } else {
                        //create message to display on user creation
                        ?>
                        <div id="dialogOk" title="Success">
                            <p>
                                <?php
                                foreach ($get->results() as $data){
                                echo '<b>' . $data->resource_brand . ' ' . $model . '</b> added successfully.';
                                ?>
                            </p>
                        </div>
                        <?php
                        }
                        }
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
                    <h1>Add a New Resource Model</h1>

                    <form action="" method="post" name="addResourceModel">
                        <select name="resource_brand_id">
                            <option value="">------------------------- Choose a Brand -------------------------</option>
                            <?php
                            $get = db::getInstance()->query('select * from ims.resource_brands order by 1');

                            if (!$get->count()) {
                                echo 'Empty List';
                            } else {

                                foreach ($get->results() as $b): ?>
                                    <option value="<?php echo escape($b->resource_brand_id); ?>">
                                        <?php echo escape($b->resource_brand); ?>
                                    </option>
                                <?php endforeach;

                            }
                            ?>
                        </select>
                        <select name="resource_type_id">
                            <option value="">------------------------- Choose a Type -------------------------</option>
                            <?php
                            $get = db::getInstance()->query('select * from ims.resource_types order by 1');

                            if (!$get->count()) {
                                echo 'Empty List';
                            } else {

                                foreach ($get->results() as $t): ?>
                                    <option value="<?php echo escape($t->resource_type_id); ?>">
                                        <?php echo escape($t->resource_type); ?>
                                    </option>
                                <?php endforeach;

                            }
                            ?>
                        </select>
                        <input type="text" name="resource_model" placeholder="New Model Name" autocomplete="off" value="<?php echo escape(input::get('resource_model')); ?>" />
                        <input type="hidden" name="token" value="<?php echo token::generate(); ?>">
                        <input type="submit" value="ADD MODEL"/>
                    </form>
                    <br>
                    <div class="form-link">
                        <a href="addResourceModel.php">Clear Form</a>
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
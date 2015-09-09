<?php 

include 'Importer.php';
// function to handle form request
function nc_admin_page() {
	
	$message = '';

	// check that the user has the required capability 
	if (!current_user_can('manage_options')){
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	
	// If request is post, process the request
	if($_SERVER['REQUEST_METHOD'] =='POST'){

		if(!empty($_POST['nc_target_url'])){
			$importer = new NCImporter($_POST['nc_author'], $_POST['nc_status']);
			if(!$importer->start($_POST['nc_target_url']))	{
				$message = $importer->message;
			}
		
		// show result after imported
?>

<?php
			if(!empty($message)){?>
				<div class="error"><p><strong><?php echo $message; ?></strong></p></div>	
<?php 	}else{?>
				<div class="updated"><p><strong><?php echo $importer->counter_imported; ?><?php _e(' posts imported.', 'nc-imported-posts' ); ?><?php echo $importer->counter_skipped; ?><?php _e(' posts skipped.', 'nc-skipped-posts' ); ?></strong></p></div>
<?php 	}// end of if empty message

		}// end of if empty url
		else{?>
			<div class="error"><p><strong><?php echo "The URL can't be empty"; ?></strong></p></div>	
		<?php }
    }// end ofif post request
	
	
	// show the form 
    echo '<div class="wrap">';
    echo "<h2>" . __( 'Facebook Posts Importer', 'nc-facebook-posts-importer' ) . "</h2>";
    ?>
    <form name="form1" method="post" action="">
        <input type="hidden" name="nc_submit_hidden" value="Y">
        
        <p><?php _e("URL:", 'nc-target-url' ); ?> 
            <input type="text" name="nc_target_url" value="" size="20">
        </p><hr />
        
        <p><?php _e("Post Author:", 'nc-author' ); ?> 
            <select  name="nc_author">
                <?php 
                        $users = get_users();
                        foreach($users as $user){?>
                    <option value="<?php echo $user->data->ID;?>"><?php echo $user->data->user_login;?></option>	
                <?php }?>
            </select>
        </p><hr />
        
        <p><?php _e("Post Status:", 'nc-status' ); ?> 
            <select  name="nc_status">
                <option value="publish">Published</option>
                <option value="draft" selected="selected">Draft</option>
            </select>
        </p><hr />
        
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Start Import') ?>" />
        </p>
    </form>
</div>

<?php
}

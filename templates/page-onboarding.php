<?php
use Ecomerciar\AstroPay\Helper\Helper;

$astropay_woo_icons = helper::get_assets_folder_url()."/img/logotype_astropay_primary_ok.png";
//$astropay_left_pane_img = helper::get_assets_folder_url()."/img/astropay_onboarding-left-pane.webp";
$astropay_left_pane_img = helper::get_assets_folder_url()."/img/transfer-web.webp";
$astropay_ok_img = helper::get_assets_folder_url()."/img/ok.svg";
$astropay_error_img = helper::get_assets_folder_url()."/img/error.svg";
$register_result = $args['register'];
$exit_url = $args['exit_url'];
$settings_url = $args['settings_url'];

$api_key = $args['api-key'];
$api_secret = $args['api-secret'];
$sdk_api_key = $args['sdk-api-key'];
?>
<div class="astropay-onboarding">
    <div class="exit-onboarding">
        <span class="close">&times;</span>
    </div>

    <div class="astropay">

        <div class="astropay-container">
            <div class="row">
                <div class="column30">
                    <div class="left-pane">
                        <img style="width:215px;height:60px;" src="<?php echo esc_url($astropay_woo_icons)?>">
                        <h1 class="welcome"><?= sprintf(__('Hi <span class="astropay-sitename">@%s!</span>', 'astropay'), get_option('blogname'))?> </h1>
                        <p><?php echo __("Thank you for starting to use <strong>AstroPay</strong> Plugin for <strong>WooCommerce</strong>. To continue with the setup process, you need to already have an <strong>AstroPay</strong> account. If you don't have it yet, <a class='create_account_link' href='https://merchants-stg.astropay.com/signup' target='_blank'> create your account here.</a>", 'astropay')?></p>
                        <img class="left-pane-img" src="<?php echo esc_url($astropay_left_pane_img)?>">
                    </div>                        
                </div>
                <div class="column70">      
                    <!-- The Modal -->
                    <div id="astropay-ok" class="modal">
                        <!-- Modal content -->
                        <div class="modal-content">

                            <span class="close">&times;</span>
                            <img src="<?php echo esc_url($astropay_ok_img)?>">
                            <h2><?php echo __('Credentials were entered successfully', 'astropay')?></h2>
                            <p><?php echo __('You have finished the initial setup for AstroPay.', 'astropay')?></p>                            
                            <a href="<?php echo esc_url(get_admin_url(null, 'admin.php?page=wc-settings&tab=checkout&section=wc_astropay'))?>">
                                <button> <?php echo __('Go to Settings', 'astropay')?> 
                                </button>
                            </a>       

                            <!--<div>
                                <img src="<?php //echo esc_url($astropay_ok_img)?>"> 
                            </div>                                      
                            <div>
                            <p><?php //echo __('Credentials were entered successfully!', 'astropay')?></p>
                            </div>                                      
                            <div>
                                <span class="close">&times;</span>
                            </div>        -->                                                 
                        </div>
                    </div>
                    <div id="astropay-error" class="modal">
                        <!-- Modal content -->
                        <div class="modal-content">                            
                        <span class="close">&times;</span>
                        <img src="<?php echo esc_url($astropay_error_img)?>">
                        <h2><?php echo __('Credentials are not correct', 'astropay')?></h2>
                        <p><?php echo __('The credentials entered are incorrect.<br>Please try again.', 'astropay')?></p>    
                        <button><?php echo __('Ok', 'astropay')?></button>
                        <!--<div>
                                <img src="<?php //echo esc_url($astropay_error_img)?>"> 
                            </div>                                      
                            <div>
                                <p><?php //echo __('Credentials are not correct', 'astropay')?></p>
                            </div>                                      
                            <div>
                                <span class="close">&times;</span>
                            </div> -->
                        </div>
                    </div>              
                    <div class="right-pane">                    
                        <h2><?php echo __('Enter your AstroPay Credentials', 'astropay')?></h2>
                        <p><?php echo __('Connect your AstroPay account with Woocommerce', 'astropay')?></p> 
                        <p><?php echo __('Don’t know your credentials?  <a class="contact_us_link" href="https://developers-wallet.astropay.com/docs/need-help" target="_blank"> Contact us </a>', 'astropay')?></p>                        
                        <br><br>
                        <form method="post" action="admin.php?page=wc-astropay-onboarding">
                            <?php settings_fields( 'wc-astropay-settings-onboarding' ); ?>
                            <?php do_settings_sections( 'wc-astropay-settings-onboarding' ); ?>
        
                            <label for="wc_astropay_api_key_sandbox" required><?php echo __('API Key','astropay') ?></label>
                            <?php woocommerce_form_field('wc_astropay_api_key_sandbox', array(
                                'type' => 'text',
                                'required' => true,                        
                            ) , isset($_POST['wc_astropay_api_key_sandbox'])? $_POST['wc_astropay_api_key_sandbox'] : $api_key );?>
                            
                            <label for="wc_astropay_api_secret_sandbox" required><?php echo __('API Secret','astropay') ?></label>
                            <?php woocommerce_form_field('wc_astropay_api_secret_sandbox', array(
                                'type' => 'password',
                                'required' => true,                        
                            ) , isset($_POST['wc_astropay_api_secret_sandbox'])? $_POST['wc_astropay_api_secret_sandbox'] : $api_secret );?>
                            
                            <label for="wc_astropay_sdk_api_key_sandbox" required><?php echo __('SDK API Secret','astropay') ?></label>
                            <?php woocommerce_form_field('wc_astropay_sdk_api_key_sandbox', array(
                                'type' => 'text',
                                'required' => true,                        
                            ) , isset($_POST['wc_astropay_sdk_api_key_sandbox'])? $_POST['wc_astropay_sdk_api_key_sandbox'] : $sdk_api_key );?>

                           <?php submit_button( __( 'Save', 'astropay' ), 'primary-button' ); ?>
                            <div class="skip">
                                <a href="<?php echo esc_url($settings_url)?>"><?php echo __("skip this step (go directly to advanced settings)", 'astropay')?></a>
                            </div>
                        </form>
        
                    </div>
                </div>
            </div>    
        </div>
    </div>
</div>
<script>
    jQuery(document).ready( function(){   
        console.log("<?php echo esc_url($exit_url);?>");           
        // When the user clicks on <span> (x), close the modal
        jQuery("span.close", jQuery("#astropay-ok")).click( function() {
            jQuery("#astropay-ok").css("display", 'none'); 
        });              
        // When the user clicks on <span> (x), close the modal
        jQuery("span.close", jQuery("#astropay-error")).click( function() {
            jQuery("#astropay-error").css("display", 'none'); 
        });  
        jQuery("button", jQuery("#astropay-error")).click( function() {
            jQuery("#astropay-error").css("display", 'none'); 
        });      
        jQuery("span.close", jQuery(".exit-onboarding")).click( function() {
            
            location.href = "<?php echo esc_url($exit_url);?>";
        });
    });
</script>

<?php if("OK"===$register_result){?>
    <script>
    jQuery(document).ready(function(){
        console.log("OK");
        jQuery("#astropay-ok").css("display", 'block');
    });
    </script>
<?php }?>

<?php if("NOK"===$register_result){?>
    <script>
    jQuery(document).ready(function(){
        console.log("NOK");
        jQuery("#astropay-error").css("display", 'block');
    });
    </script>
<?php }?>

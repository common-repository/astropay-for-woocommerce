'use strict'
jQuery( document ).ready(
	function () {
		(function ($, settings) {
			var showSpinner = function () {
				var htmlString =
				"<div class='" +
				settings.spinner_id +
				"'><div class='" +
				settings.spinner_id +
				"-center'><span class='spinner is-active'><img src='" +
				settings.spinner_url +
				"'></span></div></div>"
				htmlString     =
				htmlString +
				'<style> .' +
				settings.spinner_id +
				'{z-index:99999;width: 100%;height: 100%;position: fixed;top: 0;left: 0;opacity: 0.4;background-color:#ccc;text-align: center; } .' +
				settings.spinner_id +
				'-center{position: absolute;top: 50%; left: 50%; transform: translate(-50%, -50%); } .' +
				settings.spinner_id +
				' .spinner{ vertical-align: middle; }' +
				settings.spinner_id +
				' * img{z-index:99999;}</style>'

				$( 'body' ).prepend( htmlString )
			}

			var removeSpinner = function () {
				jQuery( '.' + settings.spinner_id ).remove()
			}

			var cta = function () {
				showSpinner()

				var dataToSend = {
					action: settings.action,
					order_id: settings.order_id,
					nonce: settings.ajax_nonce,
					pbap: settings.pbap,
				}

				$.post(
					settings.ajax_url,
					dataToSend,
					function (response) {
						if (response.success) {
                            // This is usually included in the page itself, near the 'Pay' button.
                           const APP_ID = settings.astropay_app_id; //Your APP ID SDK Credential

                            const ASTROPAY_CONFIG = {
                                environment: settings.astropay_environment, //Environments available: 'production' and 'sandbox'
                                onDepositStatusChange: (depositResult) => getDepositStatus(depositResult), //Subscribes to every transaction status update.
                                onClose: (depositResult) => iframeClosed(depositResult)
                            };

                            AstropaySDK.init(APP_ID, ASTROPAY_CONFIG); //Initiates the SDK with the configuration set above

                            const ASTROPAY_EXTERNAL_DEPOSIT_ID = response.data.deposit_external_id; //Sets the Deposit External ID generated for the deposit

                            const getDepositStatus = (depositResult) => {
                                console.log(depositResult); //Here you can do something according to the transaction current status.
								if(depositResult.status == "2"){
									// 3 seconds and go to next line of code.
									setTimeout(function(){ 
										window.location.href = wc_astropay_settings.modalCallbackURLSuccess;
									}, 3000);  
									
								}
                            };
                            const iframeClosed = (depositResult) => {
                                //console.log('action: iFrame was closed', 'Deposit Status:', depositResult); //Here you can also do a action if the user closes the iFrame, also checking the transaction status.
                            };
                            console.log("show deposit");
                            AstropaySDK.showDeposit(ASTROPAY_EXTERNAL_DEPOSIT_ID); //Show the onetouch checkout                                        
						} else {
							console.log( 'onFailurePost' )
							console.log( response )
						}
						removeSpinner()
					}
				)
                
			}

			$( '#astropay-modal-cta' ).click(
				function () {
					cta()
				}
			)
			if(settings.astropay_cta_flag){
				cta()
			}
		})( jQuery, wc_astropay_settings )
	}
)

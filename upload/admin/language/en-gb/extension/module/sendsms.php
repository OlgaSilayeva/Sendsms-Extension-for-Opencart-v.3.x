<?php
// Heading
$_['heading_title'] = 'SendSMS';
$_['heading_about'] = 'SendSMS Information';
$_['heading_history'] = 'SendSMS History';
$_['heading_test'] = 'SendSMS Test Send';
$_['heading_campaign'] = 'SendSMS Campaign';

// Titles
$_['text_module'] = 'Modules';
$_['text_success'] = 'Success: You have modified "SendSMS" module!';
$_['text_success_test_send'] = 'Success: The message has been sent!';
$_['text_success_campaign_send'] = 'Success: The messages have been sent!';
$_['text_edit'] = 'Edit "SendSMS" Module';
$_['text_history'] = 'History';
$_['text_about'] = 'Information';
$_['text_test'] = 'Test send';
$_['text_campaign'] = 'Campaign';

//Entry
$_['entry_status'] = 'Status';
$_['entry_username'] = 'Username';
$_['entry_password'] = 'Password';
$_['entry_label'] = 'Sender label';
$_['entry_simulation'] = 'Simulate sending SMS';
$_['entry_simulation_phone'] = 'Simulation phone number';
$_['entry_message'] = 'Message';
$_['entry_yes'] = 'Yes';
$_['entry_no'] = 'No';
$_['entry_characters_left'] = 'characters left';
$_['entry_available_vars'] = 'Available variables';
$_['entry_country_codes'] = 'Country Code';
$_['entry_gdpr'] = 'Unsubscribe link';
$_['entry_short'] = 'Short URL?';

// About page
$_['about_line1'] = 'To use the module, please enter your credentials on the configuration page.';
$_['about_line2'] = "Don't have a sendSMS account?";
$_['about_line3'] = 'Sign up for FREE <a href="http://www.sendsms.ro/ro" target="_blank">here</a>';
$_['about_line4'] = 'You can find out more about sendSMS <a href="http://www.sendsms.ro/ro">here</a>.';
$_['about_line5'] = "On the settings page, below the credentials, you'll find a text field for each status available in OpenCart. You will need to enter a message for the fields to which you want to send the SMS notification. If a field is empty, then the text message will not be sent.";
$_['about_line6'] = 'If you want to send a message when the status of the order changes to Completed, then you will need to fill in a message in the text field <strong>"Message: Completed"</strong>.';
$_['about_line7'] = 'You can enter variables that will be filled in according to the order data.';
$_['about_line8'] = 'Example message: <strong> Hello {billing_first_name}. Your order with number {order_number} has been completed. </strong>';
$_['about_line9'] = 'The message entered must not contain diacritics. If they are entered the letters with diacritics will be replaced with their equivalent without diacritics.';

// History page
$_['history_status'] = 'Status';
$_['history_message'] = 'Message';
$_['history_details'] = 'Details';
$_['history_content'] = 'Content';
$_['history_type'] = 'Type';
$_['history_phone'] = 'Phone';
$_['history_date'] = 'Date';
$_['history_filter'] = 'Filter';

// Test page
$_['button_send'] = 'Send';
$_['test_entry_phone'] = 'Phone number';
$_['test_entry_message'] = 'Message';

//Msg box
$_['empty_field'] = 'The field is empty';
$_['aprox_nr_msg'] = 'The approximate number of messages: ';

// Campaign page
$_['button_filter'] = 'Filter';
$_['heading_filter'] = 'Filter orders';
$_['heading_filtered'] = 'Filtered phone numbers';
$_['campaign_start_date'] = 'Start date';
$_['campaign_end_date'] = 'End date';
$_['campaign_date'] = 'Date';
$_['campaign_sum'] = 'Minimum order total';
$_['campaign_product'] = 'Bought product';
$_['campaign_billing_county'] = 'Billing county';
$_['campaign_entry_phones'] = 'Phone numbers';
$_['campaign_send_all'] = 'Send an SMS to every number.';
$_['campaign_batch_created'] = 'Your batch campaign has been created.';
$_['campaign_batch_check'] = 'We created your campaign. Go and check the batch called: ';
$_['campaign_go_to_sendsms'] = 'Go to hub.sendsms.ro';
$_['button_check_price'] = 'Check estimate price';
$_['campaign_estimate_price'] = 'Your estimate price is of ';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify "SendSMS" module!';
$_['error_phone_required'] = 'Warning: The phone number is required!';
$_['error_message_required'] = 'Warning: The message is required!';
$_['error_unable_to_create_file'] = 'Warning: Unable to open/create batch file! Please check file/folder permissions.';
$_['error_unable_to_delete_file'] = 'Warning: Unable to delete batch file! Please check file/folder permissions.';
$_['error_username_required'] = 'Warning: Configure your sendSMS.ro username!';
$_['error_password_required'] = 'Warning: Configure your sendSMS.ro password!';
$_['error_from_required'] = 'Warning: Configure your sendSMS.ro label!';
$_['error_send_a_message_first'] = 'Warning: Please send a normal message first';

//Help
$_['help_gdpr'] = 'Add unsubscribe link? (You must specify the {gdpr} key message. The {gdpr} key will be automatically replaced with the unique confirmation link. If the {gdpr} key is not specified, the confirmation link will be placed at the end of the message.)';
$_['help_short'] = 'Short URL? (Please use only links starting with https:// or http://)';

//Info
$_['info_configure_first'] = 'Please configure your the module first';
$_['info_current_balance'] = 'Your current balance is ';
$_['info_empty_message_box'] = 'The message box is empty';
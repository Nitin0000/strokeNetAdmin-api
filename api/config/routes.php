<?php
//// Routes
$app->get('/', '\Acme\Controllers\HomeController');

// $app->group('/api/v1/users', function () {
//     $this->map(['GET', 'POST'], '/get_ip_location', '\Acme\Controllers\BaseController:getIPtolocation');
//     $this->map(['GET', 'POST'], '/get_centers', '\Acme\Controllers\GlobalController:getCenters');
//     $this->map(['GET', 'POST'], '/photo_gallery', '\Acme\Controllers\GlobalController:getPhotoGallery');

//     $this->map(['GET', 'POST'], '/get_comorbidities', '\Acme\Controllers\GlobalController:getComorbidities');

//     $this->map(['GET', 'POST'], '/get_global_settings', '\Acme\Controllers\GlobalController:globalSettings');

//     $this->map(['GET', 'POST'], '/get_pages', '\Acme\Controllers\GlobalController:getPages');
//     $this->map(['GET', 'POST'], '/page/{pageId}', '\Acme\Controllers\GlobalController:getSinglePage');

//     $this->post('/contact_us', '\Acme\Controllers\GlobalController:contactUs');

//     $this->group('/sms', function () {
//         $this->post('/send_otp', '\Acme\Controllers\SMSController:sendOTPCode');
//         $this->post('/verify_otp', '\Acme\Controllers\SMSController:verifyOTP');
//     });

//     // User Authentication
//     $this->group('/auth', function () {
//         $this->post('/change_password', '\Acme\Controllers\UserController:changePassword');
//         $this->post('/edit_profile', '\Acme\Controllers\UserController:editProfile');
//         $this->map(['GET', 'POST'], '/profile', '\Acme\Controllers\UserController:userProfile');

//         $this->post('/signup', '\Acme\Controllers\AuthController:signup');
//         $this->post('/login', '\Acme\Controllers\AuthController:login');
//         $this->post('/forgotpassword', '\Acme\Controllers\AuthController:forgotPassword');

//         $this->post('/update_online_status', '\Acme\Controllers\UserController:changeOnlineStatus');

//         $this->post('/check_user_session', '\Acme\Controllers\UserController:checkUserSession');
//     });

//     $this->group('/conversations', function () {
//         $this->get('/get_all_online_users', '\Acme\Controllers\ConversationsController:fetchAllOnlineUsers'); // Created

//         $this->post('/count_unread_chats', '\Acme\Controllers\ConversationsController:checkTotalUnreadMessages'); // Created

//         $this->post('/all', '\Acme\Controllers\ConversationsController:getConversations'); // Created
//         $this->post('/create_conversation/{userId}/{patientId}', '\Acme\Controllers\ConversationsController:createConversation'); // Created

//         $this->post('/create_internal_conversation/{userId}', '\Acme\Controllers\ConversationsController:createInternalConversation'); // Created

//         $this->post('/create_last_message/{chatId}', '\Acme\Controllers\ConversationsController:createLastMessageInConversation'); // Created

//         $this->post('/create_last_message_push_notification/{chatId}', '\Acme\Controllers\ConversationsController:sendPushMessageChat'); // Created

//         $this->post('/conversation/{chatId}', '\Acme\Controllers\ConversationsController:getConversation'); // Created

//         $this->post('/delete_conversation/{chatId}/{otherUserId}', '\Acme\Controllers\ConversationsController:deleteConversation'); // Created

//         $this->post('/block_unblock_user/{userId}', '\Acme\Controllers\ConversationsController:blockUnblockUser');
//     });

//     $this->group('/patient_analysis', function () {
//         $this->get('/get_comments/{patientId}', '\Acme\Controllers\PatientAnalysisController:getComments');
//         $this->post('/post_comment', '\Acme\Controllers\PatientAnalysisController:postComment');

//         $this->post('/post_comment_push', '\Acme\Controllers\PatientAnalysisController:postCommentPushNotification');

//         $this->get('/get_online_users/{patientId}', '\Acme\Controllers\PatientAnalysisController:getOnlineUsers');
//         $this->post('/online_offline_status/{patientId}/{type}', '\Acme\Controllers\PatientAnalysisController:addRemoveFromOnlineUsers');

//         $this->get('/get_transition_statueses/{patientId}', '\Acme\Controllers\PatientAnalysisController:getTransitionStatuses');
//         $this->post('/post_transition_status', '\Acme\Controllers\PatientAnalysisController:postTransitionStatus');

//         $this->get('/get_conclusion_types/{patientId}', '\Acme\Controllers\PatientAnalysisController:getConclusionTypes');

//         $this->post('/post_conclusion', '\Acme\Controllers\PatientAnalysisController:postConclusion');
//     });

//     $this->group('/patients', function () {
//         $this->get('/user_patients', '\Acme\Controllers\PatientController:getUserPatients');

//         $this->post('/add_patient', '\Acme\Controllers\PatientController:addPatient');

//         $this->get('/search_patient/{patientCode}', '\Acme\Controllers\PatientController:findPatientWithPatientCode');

//         $this->get('/patient/{patientId}', '\Acme\Controllers\PatientController:getSinglePatient');

//         $this->post('/update_patient_presentation', '\Acme\Controllers\PatientController:updatePatientPresentation');
//         $this->post('/update_patient_basic_data', '\Acme\Controllers\PatientController:updateBasicData');
//         $this->post('/update_patient_mrs', '\Acme\Controllers\PatientController:updateMRSofPatient');
//         $this->post('/update_patient_nihss', '\Acme\Controllers\PatientController:updateNIHSSofPatient');

//         $this->post('/files/add_file', '\Acme\Controllers\PatientController:addPatientScanFile');
//         $this->post('/files/delete_file', '\Acme\Controllers\PatientController:deletePatientFile');
//         $this->post('/files/move_file', '\Acme\Controllers\PatientController:movePatientFile');

//         $this->post('/start_patient_transition_to_hub', '\Acme\Controllers\PatientController:alertHubAndStartTransition');



//         $this->post('/code_stroke_alert_manually', '\Acme\Controllers\PatientController:codeStrokeAlert');
//         $this->post('/stop_clocks', '\Acme\Controllers\PatientController:stopClockManually');

//         $this->post('/send_custom_update', '\Acme\Controllers\PatientController:postCustomPushNotficationsAndSMSs');

//     });

// });

$app->group('/api/v2/users', function () {
    $this->map(['GET', 'POST'], '/get_ip_location', '\Acme\Controllers\BaseController:getIPtolocation');
    $this->map(['GET', 'POST'], '/get_hubs', '\Acme\Controllers\GlobalController:getHubs');
    $this->map(['GET', 'POST'], '/get_centers', '\Acme\Controllers\GlobalController:getCenters');
    $this->map(['GET', 'POST'], '/photo_gallery', '\Acme\Controllers\GlobalController:getPhotoGallery');

    $this->map(['GET', 'POST'], '/get_comorbidities', '\Acme\Controllers\GlobalController:getComorbidities');

    $this->map(['GET', 'POST'], '/get_global_settings', '\Acme\Controllers\GlobalController:globalSettings');

    $this->map(['GET', 'POST'], '/get_pages', '\Acme\Controllers\GlobalController:getPages');
    $this->map(['GET', 'POST'], '/page/{pageId}', '\Acme\Controllers\GlobalController:getSinglePage');

    $this->post('/contact_us', '\Acme\Controllers\GlobalController:contactUs');

    $this->group('/sms', function () {
        $this->post('/send_otp', '\Acme\Controllers\SMSController:sendOTPCode');
        $this->post('/verify_otp', '\Acme\Controllers\SMSController:verifyOTP');
    });

    $this->group('/onesignal', function () {
        $this->get('/get_data', '\Acme\Controllers\BaseController:getOneSignalTokens');
    });

    // User Authentication
    $this->group('/auth', function () {
        $this->post('/change_password', '\Acme\Controllers\UserController:changePassword');
        $this->post('/edit_profile', '\Acme\Controllers\UserController:editProfile');
        $this->map(['GET', 'POST'], '/profile', '\Acme\Controllers\UserController:userProfile');

        $this->post('/signup', '\Acme\Controllers\AuthController:signup');
        $this->post('/login', '\Acme\Controllers\AuthController:login');
        $this->post('/forgotpassword', '\Acme\Controllers\AuthController:forgotPassword');

        $this->post('/update_online_status', '\Acme\Controllers\UserController:changeOnlineStatus');

        $this->post('/check_user_session', '\Acme\Controllers\UserController:checkUserSession');
    });

    $this->group('/conversations', function () {
        $this->get('/get_all_online_users', '\Acme\Controllers\ConversationsController:fetchAllOnlineUsers'); // Created

        $this->post('/count_unread_chats', '\Acme\Controllers\ConversationsController:checkTotalUnreadMessages'); // Created

        $this->post('/all', '\Acme\Controllers\ConversationsController:getConversations'); // Created
        $this->post('/create_conversation/{userId}/{patientId}', '\Acme\Controllers\ConversationsController:createConversation'); // Created

        $this->post('/create_internal_conversation/{userId}', '\Acme\Controllers\ConversationsController:createInternalConversation'); // Created

        $this->post('/create_last_message/{chatId}', '\Acme\Controllers\ConversationsController:createLastMessageInConversation'); // Created

        $this->post('/create_last_message_push_notification/{chatId}', '\Acme\Controllers\ConversationsController:sendPushMessageChat'); // Created

        $this->post('/conversation/{chatId}', '\Acme\Controllers\ConversationsController:getConversation'); // Created

        $this->post('/delete_conversation/{chatId}/{otherUserId}', '\Acme\Controllers\ConversationsController:deleteConversation'); // Created

        $this->post('/block_unblock_user/{userId}', '\Acme\Controllers\ConversationsController:blockUnblockUser');
    });

    $this->group('/patient_analysis', function () {
        $this->get('/get_comments/{patientId}', '\Acme\Controllers\PatientAnalysisController:getComments');
        $this->post('/post_comment', '\Acme\Controllers\PatientAnalysisController:postComment');

        $this->post('/post_comment_push', '\Acme\Controllers\PatientAnalysisController:postCommentPushNotification');

        $this->get('/get_online_users/{patientId}', '\Acme\Controllers\PatientAnalysisController:getOnlineUsers');

        $this->post('/online_offline_status/{patientId}/{type}', '\Acme\Controllers\PatientAnalysisController:addRemoveFromOnlineUsers');

        $this->get('/get_transition_statueses/{patientId}', '\Acme\Controllers\PatientAnalysisController:getTransitionStatuses');
        $this->post('/post_transition_status', '\Acme\Controllers\PatientAnalysisController:postTransitionStatus');

        $this->get('/get_conclusion_types/{patientId}', '\Acme\Controllers\PatientAnalysisController:getConclusionTypes');

        $this->post('/post_conclusion', '\Acme\Controllers\PatientAnalysisController:postConclusion');
    });

    $this->group('/patients', function () {

        // $this->get('/test_call', '\Acme\Controllers\PatientController:testCall');

        $this->get('/get_times/{patientId}', '\Acme\Controllers\PatientController:getPatientTimes');

        $this->get('/get_bulk_timings/{patientType}/{timePeriod}', '\Acme\Controllers\PatientController:calculateBulkPatientTimings');

        $this->get('/user_patients', '\Acme\Controllers\PatientController:getUserPatients');

        $this->post('/add_patient', '\Acme\Controllers\PatientController:addPatient');

        $this->get('/search_patient/{patientCode}', '\Acme\Controllers\PatientController:findPatientWithPatientCode');

        $this->get('/patient/{patientId}', '\Acme\Controllers\PatientController:getSinglePatient');

        $this->post('/update_patient_presentation', '\Acme\Controllers\PatientController:updatePatientPresentation');

        $this->post('/update_patient_complications', '\Acme\Controllers\PatientController:updatePatientComplications');

        $this->post('/update_scans_uploaded', '\Acme\Controllers\PatientController:scansUploadedAlertToTeam');

        $this->post('/update_patient_scan_times', '\Acme\Controllers\PatientController:updateScanTimesofPatient');

        $this->post('/update_patient_medications', '\Acme\Controllers\PatientController:updatePatientMedications');

        $this->post('/update_patient_contradictions', '\Acme\Controllers\PatientController:updatePatientContradictions');

        $this->post('/update_patient_basic_data', '\Acme\Controllers\PatientController:updateBasicData');
        $this->post('/update_patient_mrs', '\Acme\Controllers\PatientController:updateMRSofPatient');
        $this->post('/update_patient_nihss', '\Acme\Controllers\PatientController:updateNIHSSofPatient');

        $this->post('/files/add_file', '\Acme\Controllers\PatientController:addPatientScanFile');
        $this->post('/files/delete_file', '\Acme\Controllers\PatientController:deletePatientFile');
        $this->post('/files/move_file', '\Acme\Controllers\PatientController:movePatientFile');

        $this->post('/start_patient_transition_to_hub', '\Acme\Controllers\PatientController:alertHubAndStartTransition');

        $this->post('/start_patient_transition_to_spoke', '\Acme\Controllers\PatientController:alertSpokeAndStartTransition');

        $this->post('/code_stroke_alert_manually', '\Acme\Controllers\PatientController:codeStrokeAlert');
        $this->post('/stop_clocks', '\Acme\Controllers\PatientController:stopClockManually');

        $this->get('/test_api', '\Acme\Controllers\PatientController:testAPI');

        $this->get('/get_hub_spoke_centers/{patientId}', '\Acme\Controllers\PatientController:getHubSpokeCenters');
    });
});
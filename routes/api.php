<?php

use App\Http\Controllers\Admin\DigitalAssetController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmailVerify;
use App\Http\Controllers\Api\MarketplaceItemController;
use App\Http\Controllers\Api\ResetPassword;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [ApiController::class, 'logout']);
});*/

/**
 * STUDENT APIs
 **/
Route::group(['prefix' => 'student'], function () {

    // Auth
    Route::middleware('guest')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
        //Reset Password
        Route::post('send/otp/reset/password', [ResetPassword::class, 'sendResetPasswordOtp']);
        Route::post('verify/otp/reset/password', [ResetPassword::class, 'verifyResetPasswordOtp']);
        Route::post('reset/password', [ResetPassword::class, 'resetPassword']);
    });


    //Authenticated APIs
    Route::group(['middleware' => 'auth:sanctum', 'isStudent'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('email/verify', [EmailVerify::class, 'Verify']);
        Route::post('resend/email/verify', [EmailVerify::class, 'resendVerificationOtp']);
        // User
        Route::patch('update/user/profile', [UserController::class, 'updateUserProfile']);
        Route::delete('delete/user/profile', [UserController::class, 'deleteUserProfile']);
        //Marketplace Items

        Route::get('/packages', [MarketplaceItemController::class, 'getPackages']);
        Route::get('/digital-assets', [MarketplaceItemController::class, 'getDigitalAssets']);
    });


    /*    Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('dashboard',[StudentApiController::class,'dashboard']);
            Route::get('subjects', [StudentApiController::class, 'subjects']);
            Route::get('class-subjects', [StudentApiController::class, 'classSubjects']);
            Route::post('select-subjects', [StudentApiController::class, 'selectSubjects']);
            Route::get('parent-details', [StudentApiController::class, 'getParentDetails']);
            Route::get('timetable', [StudentApiController::class, 'getTimetable']);
            Route::get('lessons', [StudentApiController::class, 'getLessons']);
            Route::get('lesson-topics', [StudentApiController::class, 'getLessonTopics']);
            Route::get('assignments', [StudentApiController::class, 'getAssignments']);
            Route::post('submit-assignment', [StudentApiController::class, 'submitAssignment']);
            Route::post('edit-assignment', [StudentApiController::class, 'editAssignmentSubmission']);
            Route::post('delete-assignment-submission', [StudentApiController::class, 'deleteAssignmentSubmission']);
            Route::get('attendance', [StudentApiController::class, 'getAttendance']);
            Route::get('announcements', [StudentApiController::class, 'getAnnouncements']);
            Route::get('get-exam-list', [StudentApiController::class, 'getExamList']); // Exam list Route
            Route::get('get-exam-details', [StudentApiController::class, 'getExamDetails']); // Exam Details Route
            Route::get('exam-marks', [StudentApiController::class, 'getExamMarks']); // Exam Details Route

            // online exam routes
            Route::get('get-online-exam-list', [StudentApiController::class, 'getOnlineExamList']); // Get Online Exam List Route
            Route::get('get-online-exam-questions', [StudentApiController::class, 'getOnlineExamQuestions']); // Get Online Exam Questions Route
            Route::post('submit-online-exam-answers', [StudentApiController::class, 'submitOnlineExamAnswers']); // Submit Online Exam Answers Details Route
            Route::get('get-online-exam-result-list', [StudentApiController::class, 'getOnlineExamResultList']); // Online exam result list Route
            Route::get('get-online-exam-result', [StudentApiController::class, 'getOnlineExamResult']); // Online exam result  Route

            //reports
            Route::get('get-online-exam-report', [StudentApiController::class, 'getOnlineExamReport']); // Online Exam Report Route
            Route::get('get-assignments-report', [StudentApiController::class, 'getAssignmentReport']); // Assignment Report Route

            // profile data
            Route::get('get-profile-data', [StudentApiController::class, 'getProfileDetails']); // Get Profile Data
            Route::get('get-notification',[StudentApiController::class, 'getNotifications']); // Get Notification Data

            Route::get('get-user-list',[StudentApiController::class,'getChatUserList']);
            Route::post('send-message',[StudentApiController::class,'sendMessage']);
            Route::post('get-user-message',[StudentApiController::class,'getUserChatMessage']);
            Route::post('read-all-message',[StudentApiController::class, 'readAllMessages']);

            //fees
            Route::get('fees-details', [StudentApiController::class, 'getFeesDetails']); //Fees Details
            Route::post('add-fees-transaction', [StudentApiController::class, 'storeFeesTransaction']); //Fees Details
            Route::post('store-fees', [StudentApiController::class, 'storeFees']); //Store Fees
            Route::get('fees-paid-list', [StudentApiController::class, 'feesPaidList']); //Fees Details
            Route::get('fees-paid-receipt-pdf', [StudentApiController::class, 'feesPaidReceiptPDF']); //Fees Receipt
            Route::get('fees-transactions-list', [StudentApiController::class, 'getFeesPaymentTransactions']); //Fees Payment Transaction Details
            Route::post('fail-payment-transaction', [StudentApiController::class, 'failPaymentTransactionStatus']); // Make Payment Transaction Fail API

              //fee notification
            Route::get('send-fee-notification', [StudentApiController::class, 'sendFeeNotification']);

            Route::post('apply-leave',[StudentApiController::class, 'applyLeave']);
            Route::post('get-leave-list',[StudentApiController::class, 'getMyLeave']);
            Route::post('delete-leave',[StudentApiController::class, 'deleteLeave']);
        });*/
});



/**
 * TEACHER APIs
 **/
/*Route::group(['prefix' => 'teacher'], function () {
    //Non Authenticated APIs
    Route::post('login', [TeacherApiController::class, 'login']);
    //Authenticated APIs
    Route::group(['middleware' => ['auth:sanctum',]], function () {
        Route::get('dashboard',[TeacherApiController::class,'dashboard']);
        Route::get('classes', [TeacherApiController::class, 'classes']);

        Route::get('subjects', [TeacherApiController::class, 'subjects']);

        //Assignment
        Route::get('get-assignment', [TeacherApiController::class, 'getAssignment']);
        Route::post('create-assignment', [TeacherApiController::class, 'createAssignment']);
        Route::post('update-assignment', [TeacherApiController::class, 'updateAssignment']);
        Route::post('delete-assignment', [TeacherApiController::class, 'deleteAssignment']);

        //Assignment Submission
        Route::get('get-assignment-submission', [TeacherApiController::class, 'getAssignmentSubmission']);
        Route::post('update-assignment-submission', [TeacherApiController::class, 'updateAssignmentSubmission']);

        //File
        Route::post('delete-file', [TeacherApiController::class, 'deleteFile']);
        Route::post('update-file', [TeacherApiController::class, 'updateFile']);

        //Lesson
        Route::get('get-lesson', [TeacherApiController::class, 'getLesson']);
        Route::post('create-lesson', [TeacherApiController::class, 'createLesson']);
        Route::post('update-lesson', [TeacherApiController::class, 'updateLesson']);
        Route::post('delete-lesson', [TeacherApiController::class, 'deleteLesson']);

        //Topic
        Route::get('get-topic', [TeacherApiController::class, 'getTopic']);
        Route::post('create-topic', [TeacherApiController::class, 'createTopic']);
        Route::post('update-topic', [TeacherApiController::class, 'updateTopic']);
        Route::post('delete-topic', [TeacherApiController::class, 'deleteTopic']);

        //Announcement
        Route::get('get-announcement', [TeacherApiController::class, 'getAnnouncement']);
        Route::post('send-announcement', [TeacherApiController::class, 'sendAnnouncement']);
        Route::post('update-announcement', [TeacherApiController::class, 'updateAnnouncement']);
        Route::post('delete-announcement', [TeacherApiController::class, 'deleteAnnouncement']);

        Route::get('get-attendance', [TeacherApiController::class, 'getAttendance']);
        Route::post('submit-attendance', [TeacherApiController::class, 'submitAttendance']);


        //Exam
        Route::get('get-exam-list', [TeacherApiController::class, 'getExamList']); // Exam list Route
        Route::get('get-exam-details', [TeacherApiController::class, 'getExamDetails']); // Exam Details Route
        Route::post('submit-exam-marks/subject', [TeacherApiController::class, 'submitExamMarksBySubjects']); // Submit Exam Marks By Subjects Route
        Route::post('submit-exam-marks/student', [TeacherApiController::class, 'submitExamMarksByStudent']); // Submit Exam Marks By Students Route

        Route::group(['middleware' => ['auth:sanctum', 'checkStudent']], function () {
            Route::get('get-student-result', [TeacherApiController::class, 'GetStudentExamResult']); // Student Exam Result
            Route::get('get-student-marks', [TeacherApiController::class, 'GetStudentExamMarks']); // Student Exam Marks
        });

        //Student List
        Route::get('student-list', [TeacherApiController::class, 'getStudentList']);
        Route::get('student-details', [TeacherApiController::class, 'getStudentDetails']);

        //Schedule List
        Route::get('teacher_timetable', [TeacherApiController::class, 'getTeacherTimetable']);

        //Profile Detials
        Route::get('get-profile-details', [TeacherApiController::class, 'getProfileDetails']);
        Route::get('get-notification',[TeacherApiController::class, 'getNotifications']); // Get Notification Data

        Route::get('get-user-list',[TeacherApiController::class,'getChatUserList']);
        Route::post('send-message',[TeacherApiController::class,'sendMessage']);
        Route::post('get-user-message',[TeacherApiController::class,'getUserChatMessage']);
        Route::post('read-all-message',[TeacherApiController::class, 'readAllMessages']);

        Route::get('get-student-result-pdf',[TeacherApiController::class, 'getStudentResultPdf']);

        Route::post('apply-leave',[TeacherApiController::class, 'applyLeave']);
        Route::post('get-leave-list',[TeacherApiController::class, 'getMyLeave']);
        Route::post('delete-leave',[TeacherApiController::class, 'deleteLeave']);

        Route::post('get-student-leave-list',[TeacherApiController::class, 'getStudentLeaveList']);
        Route::post('student-leave-status-update',[TeacherApiController::class, 'studentLeaveStatusUpdate']);

        Route::post('update-timetable-link', [TeacherApiController::class, 'updateTimetableLink']);
    });
});*/

/**
 * GENERAL APIs
 **/
/*Route::get('holidays', [ApiController::class, 'getHolidays']);
Route::get('sliders', [ApiController::class, 'getSliders']);
Route::get('current-session-year', [ApiController::class, 'getCurrentSessionYear']);
Route::get('settings', [ApiController::class, 'getSettings']);
Route::post('forgot-password', [ApiController::class, 'forgotPassword']);
Route::get('get-events-list',[ApiController::class,'getEvents']);
Route::get('get-events-details',[ApiController::class,'getEventsDetails']);
Route::get('get-session-year',[ApiController::class,'getSessionYear']);

Route::group(['middleware' => ['auth:sanctum',]], function () {
Route::post('change-password', [ApiController::class, 'changePassword']);

});*/

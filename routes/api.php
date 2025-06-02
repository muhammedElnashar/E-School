<?php

use App\Http\Controllers\StudentApi\EmailVerify;
use App\Http\Controllers\StudentApi\MarketplaceItemController;
use App\Http\Controllers\StudentApi\ResetPassword;
use App\Http\Controllers\StudentApi\SubmissionController;
use App\Http\Controllers\TeacherApi\AuthController;
use App\Http\Controllers\TeacherApi\LessonController;
use App\Http\Controllers\TeacherApi\TeacherSubjectController;
use App\Http\Controllers\TeacherApi\UserController;
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


/**
 * STUDENT APIs
 **/
Route::group(['prefix' => 'student'], function () {

    // Auth
    Route::middleware('guest')->group(function () {
        Route::post('/register', [\App\Http\Controllers\StudentApi\AuthController::class, 'studentRegister']);
        Route::post('/login', [\App\Http\Controllers\StudentApi\AuthController::class, 'studentLogin']);
        Route::post('/auth/google/callback', [\App\Http\Controllers\StudentApi\AuthController::class, 'handleGoogleCallback']);
        //Reset Password
        Route::post('send/otp/reset/password', [ResetPassword::class, 'sendResetPasswordOtp']);
        Route::post('verify/otp/reset/password', [ResetPassword::class, 'verifyResetPasswordOtp']);
        Route::post('reset/password', [ResetPassword::class, 'resetPassword']);
    });

    //Authenticated APIs
    Route::group(['middleware' => ['auth:sanctum', 'isStudent']], function () {
        Route::post('logout', [\App\Http\Controllers\StudentApi\AuthController::class, 'studentLogout']);
        Route::post('email/verify', [EmailVerify::class, 'Verify']);
        Route::post('resend/email/verify', [EmailVerify::class, 'resendVerificationOtp']);
        // User
        Route::patch('update/user/profile', [\App\Http\Controllers\StudentApi\UserController::class, 'updateStudentProfile']);
        Route::delete('delete/user/profile', [\App\Http\Controllers\StudentApi\UserController::class, 'deleteStudentProfile']);
        // User Info
        Route::get('user/info/{id}', [\App\Http\Controllers\StudentApi\UserInfoController::class, 'getUserInfo']);
        Route::get('user/data', [\App\Http\Controllers\StudentApi\UserInfoController::class, 'userCreditsGroupedByPackage']);
        //Marketplace Items
        Route::get('/packages', [MarketplaceItemController::class, 'getPackages']);
        Route::get('/digital-assets', [MarketplaceItemController::class, 'getDigitalAssets']);
        Route::get('/subjects', [MarketplaceItemController::class, 'getAllSubject']);
        //Student List
        Route::get('/lessons/list',[\App\Http\Controllers\StudentApi\LessonStudentsController::class,'list']);
        Route::post('/assign/lesson',[\App\Http\Controllers\StudentApi\LessonStudentsController::class,'assign']);
        Route::delete('/cancel/lesson/{id}',[\App\Http\Controllers\StudentApi\LessonStudentsController::class,'cancel']);
        Route::post('/submission', [SubmissionController::class, 'store']);
        Route::get("announcements",[\App\Http\Controllers\StudentApi\Announcement::class, 'index']);

    });


});
/**
 * Teacher APIs
 **/
Route::group(['prefix' => 'teacher'], function () {

    // Auth
    Route::middleware('guest')->group(function () {
        Route::post('/login', [AuthController::class, 'teacherLogin']);
    });
    //Authenticated APIs
    Route::group(['middleware' => ['auth:sanctum','isTeacher']], function () {
        Route::post('teacher/logout', [AuthController::class, 'TeacherLogout']);
        // User
        Route::patch('update/teacher/profile', [UserController::class, 'updateTeacherProfile']);
        //Lessons
        Route::get('/lesson', [LessonController::class, 'index']);
        Route::post('/lesson', [LessonController::class, 'store']);
        Route::put('/lesson/{lesson}', [LessonController::class, 'update']);
        Route::delete('/lesson/{lesson}', [LessonController::class, 'destroy']);
        Route::apiResource('teacher/subjects', TeacherSubjectController::class);
//        Route::middleware('auth:sanctum')->get('lesson/{lesson}/join', [LessonController::class, 'joinLesson']);
        // Assignments
        Route::get('/assignments', [\App\Http\Controllers\TeacherApi\AssignmentController::class, 'index']);
        Route::post('/assignments', [\App\Http\Controllers\TeacherApi\AssignmentController::class, 'store']);
        Route::get('/assignments/{assignment}', [\App\Http\Controllers\TeacherApi\AssignmentController::class, 'show']);
        Route::put('/assignments/{assignment}', [\App\Http\Controllers\TeacherApi\AssignmentController::class, 'update']);
        Route::delete('/assignments/{assignment}', [\App\Http\Controllers\TeacherApi\AssignmentController::class, 'destroy']);
        Route::get('/assignments/{assignment}/submissions', [\App\Http\Controllers\TeacherApi\SubmissionController::class, 'showAssignmentWithSubmissions']);
        Route::put('/submissions/{submission}', [\App\Http\Controllers\TeacherApi\SubmissionController::class, 'updateSubmission']);

    });
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

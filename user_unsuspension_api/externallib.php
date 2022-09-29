<?php

use core_completion\progress;
require_once(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/externallib.php');
require_once($CFG->dirroot.'/user/lib.php');
//require_once($CFG->dirroot.'/course/lib.php');

class local_custom_service_external extends external_api {


      /**
     * Performs the actual user unsuspension by updating the users table
     *
     * @param \stdClass $user
     */
    //static public final function do_unsuspend_user($user) {
     public static function do_unsuspend_user($user) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/lib.php');
        // Piece of code taken from /admin/user.php so we dance just like moodle does.
        if ($user = $DB->get_record('user', array('id' => $user->id,
                'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0))) {
            if ($user->suspended != 0) {
                $user->suspended = 0;
                user_update_user($user, false, true);
                // Process email id applicable.
                $emailsent = (self::process_user_unsuspended_email($user) === true);
                // Trigger event.
                $event = event\user_unsuspended::create(
                        array(
                            'objectid' => $user->id,
                            'relateduserid' => $user->id,
                            'context' => \context_user::instance($user->id),
                            'other' => array()
                            )
                        );
                $event->trigger();
                // Create status record.
                self::process_status_record($user, 'unsuspended', $emailsent);
                return true;
            }
            ///////////
            $user_unsuspended = [
                'objectid' => $user->id
                'message'=>'Success',
                'unsuspended'=>$user
                ];
            return $user_unsuspended;
            ////////////
        }
        return false;
    }

    public static function do_unsuspend_user_returns() {
        return new external_single_structure(
                array(
                    'objectid' => new external_value(PARAM_TEXT, 'User ids'), //string changed from ids
                    'message'=> new external_value(PARAM_TEXT, 'success message'),
                    'unsuspended'=>new external_value(PARAM_TEXT,'User Un-suspended') //string changed from updated
                )
            );
    }


    ////////
    // public static function update_courses_lti_parameters() {
    //     return new external_function_parameters(
    //         array(
    //             'courseids' => new external_value(PARAM_TEXT, 'Course Ids')                
    //         )
    //     );
    // }
    // public static function update_courses_lti($courseids) {
    //     global $DB,$CFG;
    //     $lti_updated = [];
    //     $status = false;
    //     //print_object($courseids);
    //     $sql = "SELECT cm.id as moduleid,cm.instance ltiid,cm.section as section,lt.name as ltiname,lt.grade as grade,lt.timecreated,lt.timemodified,c.id as courseid,gd.id as category
    //         FROM {course} c 
    //         JOIN {course_modules} cm ON c.id = cm.course 
    //         JOIN {lti} lt ON cm.instance = lt.id 
    //         JOIN {grade_categories} gd ON gd.courseid = c.id
    //         WHERE cm.module =15 AND c.id in (".$courseids.")";
    //     $modules = $DB->get_records_sql($sql);
    //     $all_module = array();
    //     $count = 0;
    //     foreach ($modules as $key => $value) {
    //         if($DB->record_exists('grade_items',array('courseid'=>$value->courseid,'categoryid'=>$value->category,'itemtype'=>'mod','itemmodule'=>'lti','iteminstance'=>$value->ltiid))){
    //             //$all_module[] = $value;
    //         }else{
    //             $new_grade_item = new stdClass();
    //             $new_grade_item->courseid = $value->courseid;
    //             $new_grade_item->categoryid = $value->category;
    //             $new_grade_item->itemname = $value->ltiname;
    //             $new_grade_item->itemtype = 'mod';
    //             $new_grade_item->itemmodule = 'lti';
    //             $new_grade_item->iteminstance = $value->ltiid;
    //             $new_grade_item->itemnumber = 0;
    //             $new_grade_item->grademax = $value->grade;
    //             $new_grade_item->timecreated = $value->timecreated;
    //             $new_grade_item->timemodified = $value->timemodified;

    //             $insert_new_gradeitem = $DB->insert_record('grade_items',$new_grade_item);
    //             $count++;
    //         }
    //     }
        
    //     $lti_updated = [
    //                     'ids'=>$courseids,
    //                     'message'=>'Success',
    //                     'updated'=>$count
    //                     ];
    //     return $lti_updated;
    // }
    // public static function update_courses_lti_returns() {
    //     return new external_single_structure(
    //             array(
    //                 'ids' => new external_value(PARAM_TEXT, 'course ids'),
    //                 'message'=> new external_value(PARAM_TEXT, 'success message'),
    //                 'updated'=>new external_value(PARAM_TEXT,'Items Updated')
    //             )
    //         );
    // }

}
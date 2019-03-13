<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Traits\CommonTrait;
use App\Interfaces\ProJobsInterface;
use App\Http\Requests\ProJob\AddProJobRequest;
use App\Http\Requests\ProJob\EditProJobRequest;
use App\Http\Requests\ProJob\InviteProJobRequest;
use App\Models\ProJob;
use App\Models\ProJobSkill;
use App\Models\ProJobFile;
use App\Models\ProJobInvite;
use App\Models\Skill;
use Config;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Image;
use Lcobucci\JWT\Parser;
use Mail;
use Response;
use Illuminate\Support\Str;

class ProJobsController extends Controller implements ProJobsInterface
{
    use CommonTrait;

    
    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/pro-jobs/add",
     *   summary="add job",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Jobs"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "title",
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "description",
     *   ),
     *   @SWG\Parameter(
     *     name="start_date_time",
     *     in="formData",
     *     required=false,
     *     type="integer",
     *     description = "start date time",
     *   ),
     *   @SWG\Parameter(
     *     name="end_date_time",
     *     in="formData",
     *     required=false,
     *     type="integer",
     *     description = "end date time",
     *   ),
     *  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function add(AddProJobRequest $request)
    {   
        $attachFiles = $request->file('attach_file');


        $job_id = ProJob::Create([
                    'user_id' => Auth::user()->id,
                    'title' => isset($request->title) ? $request->title : '',
                    'description' => isset($request->description) ? $request->description : '',
                    'start_date_time' => isset($request->start_date_time) ? $request->start_date_time : '',
                    'end_date_time' => isset($request->end_date_time) ? $request->end_date_time : '',
                    'created_at' => time(),
                    'updated_at' => time()
        ]);
        if(isset($job_id) && isset($request->pro_job_skills)) # if skill added 
        $save_job_skill_set = $this->saveJobSkills($request->pro_job_skills, $job_id->id); #save job skill sets
        
        if (isset($attachFiles))  # if attach files
        $attach_job_file = $this->attachFiles($attachFiles, $job_id->id);
                    

        if($job_id)
             $data = \Config::get('success.pro_job_added'); #success message
       
        else
            $data = \Config::get('error.pro_job_added'); #error message

        $data['data'] = (object) [];    
        return Response::json($data);     
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/pro-jobs/edit",
     *   summary="edit job",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Jobs"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="job_id",
     *     in="formData",
     *     required=true,
     *     type="integer",
     *     description = "job id",
     *   ),
     *   @SWG\Parameter(
     *     name="title",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "title",
     *   ),
     *   @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     required=false,
     *     type="string",
     *     description = "description",
     *   ),
     *   @SWG\Parameter(
     *     name="start_date_time",
     *     in="formData",
     *     required=false,
     *     type="integer",
     *     description = "start date time",
     *   ),
     *   @SWG\Parameter(
     *     name="end_date_time",
     *     in="formData",
     *     required=false,
     *     type="integer",
     *     description = "end date time",
     *   ),
     *  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function edit(EditProJobRequest $request)
    {   
        $attachFiles = $request->file('attach_file');
        $edit_job = ProJob::where('id',$request->job_id)
                    ->update([
                    'user_id' => Auth::user()->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'start_date_time' => $request->start_date_time,
                    'end_date_time' => $request->end_date_time,
                    'updated_at' => time()
        ]);

        if(isset($request->job_id) && isset($request->pro_job_skills))
        $save_job_skill_set = $this->saveJobSkills($request->pro_job_skills, $request->job_id); #save job skill sets
        
        if (isset($attachFiles))  # if attach files
        $attach_job_file = $this->attachFiles($attachFiles, $request->job_id);    
      
                    
        if(isset($edit_job))
             $data = \Config::get('success.pro_job_edited'); #success message
       
        else
            $data = \Config::get('error.pro_job_edited'); #error message

        $data['data'] = (object) [];    
        return Response::json($data);  
    }


    /* function to save jobs skills */

    private function saveJobSkills($skills_sets, $job_id) {

       
        if (isset($job_id)) { #if job is in edit mode remove exiting job skills
            ProJobSkill::where('pro_job_id', $job_id)->delete();
        }
        $skill_arr = [];
        foreach ($skills_sets as $key => $skills_set) {
            $check_skill = Skill::find($skills_set);
            if(isset($check_skill)){
            $skill_arr[$key]['pro_job_id'] = $job_id;
            $skill_arr[$key]['skill_id'] = $skills_set;
            $skill_arr[$key]['created_at'] = time();
            $skill_arr[$key]['updated_at'] = time();
            }
        }    

        ProJobSkill::insert($skill_arr); #save job skills   
        return 1;
    }

    /* function attach files regrading job */

    private function attachFiles($attach_files, $job_id = Null) {

        if (isset($job_id)) { #if job is in edit mode remove existing files
            $get_job_files = ProJobFile::where('pro_job_id', $job_id)->get();
            foreach ($get_job_files as $get_job_file) {

                if (file_exists(public_path('storage/job_files/') . $get_job_file['file_name'])) { #remove main job files
                    unlink('storage/job_files/' . $get_job_file['file_name']);
                }
            }
            ProJobFile::where('pro_job_id', $job_id)->delete();
        }
        $attach_files = $attach_files;
        $input = [];
        foreach ($attach_files as $key => $attach_file) {

            /* Upload file */
            $file = $attach_file;
            $dynamic_name = $this->imageDynamicName();
            $filename = time() . '-' . $dynamic_name . '.' . $file->getClientOriginalExtension();  #get Dynamic Name
            $destinationPath = public_path('storage/job_files/');      #file Path
            $file->move($destinationPath, $filename);  #Move file into folder
            $input[$key]['pro_job_id'] = $job_id;
            $input[$key]['file_name'] = $filename;
            $input[$key]['name'] =  $file->getClientOriginalName();
            $input[$key]['type'] = $file->getClientOriginalExtension();
            $input[$key]['created_at'] = time();
            $input[$key]['updated_at'] = time();
        }

        ProJobFile::insert($input);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/pro-jobs/delete",
     *   summary="delete job",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Jobs"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="job_id",
     *     in="formData",
     *     required=true,
     *     type="integer",
     *     description = "job id",
     *   ),
     *  
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */

    public function delete(Request $request)
    {
        $delete_job = ProJob::where('id',$request->job_id)
                            ->where('user_id',Auth::user()->id)
                            ->delete();
        if(isset($delete_job))
             $data = \Config::get('success.pro_job_deleted'); #success message
       
        else
            $data = \Config::get('error.pro_job_deleted'); #error message

        $data['data'] = (object) [];    
        return Response::json($data); 
        
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/pro-jobs/getSingleJob",
     *   summary="get Single Job",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Jobs"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="job_id",
     *     in="query",
     *     required=true,
     *     type="integer",
     *     description = "job id",
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    

    public function getSingleJob(Request $request)
    {
       
        $get_single_job = ProJob::where('id',$request->job_id)
                            ->first();
        if(isset($get_single_job)){
             $data = \Config::get('success.pro_job_get'); #success message
             $data['data'] = $get_single_job;   
        }
        else{
            $data = \Config::get('error.pro_job_get'); #error message
            $data['data'] = (object) [];    
        }

        
        return Response::json($data); 

    }

   /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Get(
     *   path="/pro-jobs/getAllJobs",
     *   summary="Get all jobs",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Jobs"},
     * @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     * @SWG\Parameter(
     *     name="q",
     *     in="query",
     *     required=false,
     *     type="string",
     *     description = ""
     *   ),
     * @SWG\Parameter(
     *     name="owner",
     *     in="query",
     *     required=false,
     *     type="string",
     *     description = "send 'me' or 'other' or 'all'"
     *   ),
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
     public function getAllJobs(Request $request)
    {   

        $owner = isset($request->owner) ? $request->owner : 'all';  # send 'me' or 'other' 
        switch($owner) {
        case 'me': 
            $get_all_jobs = ProJob::where('user_id',Auth::user()->id)->paginate(\Config::get('variable.page_per_record'));
        break;
        case 'other': 
            $get_all_jobs = ProJob::where('user_id','!=',Auth::user()->id)->paginate(\Config::get('variable.page_per_record'));    
        break;
        case 'all': 
            $get_all_jobs = ProJob::paginate(\Config::get('variable.page_per_record'));  
        break;
        }
       

       if(isset($get_all_jobs)){
             $data = \Config::get('success.pro_job_get'); #success message
             $data['data'] = $get_all_jobs;   
        }
        else{
            $data = \Config::get('error.pro_job_get'); #error message
            $data['data'] = (object) [];    
        }   
        return Response::json($data); 
    }


     /**
     * @return \Illuminate\Http\JsonResponse
     *
     *
     *  @SWG\Post(
     *   path="/pro-jobs/invite-job",
     *   summary="invite job",
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   tags={"Jobs"},
     *  @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     required=true,
     *     description = "Enter Token",
     *     type="string",
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="formData",
     *     required=true,
     *     description="Enter user_id",
     *     type="integer",
     *   ),
     *   @SWG\Parameter(
     *     name="pro_job_id",
     *     in="formData",
     *     required=true,
     *     type="integer",
     *     description = "Enter job_id",
     *   ),   
     *   @SWG\Response(response=200, description="Success"),
     *   @SWG\Response(response=400, description="Failed"),
     *   @SWG\Response(response=405, description="Undocumented data"),
     *   @SWG\Response(response=500, description="Internal server error")
     * )
     *
     */
    public function inviteJob(InviteProJobRequest $request){

       
        $requested_data = $request->all();

        if($requested_data['data']['id'] != $requested_data['user_id'])
        {   
            $array['user_id'] = $requested_data['user_id'];
            $array['pro_job_id'] = $requested_data['pro_job_id'];
            $array['created_at'] = time();
            $array['updated_at'] = time();
            $inivte_job = ProJobInvite::create($array);
        }
        
        if ($inivte_job) {
            $data = \Config::get('success.invite_job');
            $data['data'] = (object) [];
            return Response::json($data);
        } else {
            $data = \Config::get('error.invite_job');
            $data['data'] = (object) [];
            return Response::json($data);
        }

    }



  



}

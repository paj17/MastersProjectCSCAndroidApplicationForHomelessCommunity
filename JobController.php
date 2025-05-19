<?php
namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Models\User;
use App\Models\Job;
use App\Models\UserRegistration;
use Exception;
use Validator;
use Log;
class JobController extends Controller
{
  

    /// get friend List///
    public function getJobList(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'userId'        => 'required',
        //     'userToken'     => 'required',
        // ]);

       
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 400);
        // }

       // $input      = $request->all();
        // $userId     = $request->input('userId');
        // $userToken  = $request->input('userToken');

          //-------------------Message------------------------------
          $msg1 = "Job List Found";
          $msg2 = "Job List Not Found";
          $msg3 = "You are not authorized please login again";
        //-------------------Message------------------------------
        // $verifiedUserId = Controller::checkToken($input['userId'],$input['userToken']);
        // if($verifiedUserId == $userId)
        // {
           
                $jobData = Job::query()
                            ->where('isActive', 1) 
                            ->get();
          
           
            $jobArray=[];
            if(count($jobData)>0)
            {
                foreach($jobData as $job)
                {
                            $jobArray[] = array(
                                'jobId'        => $job->id,
                                'jobName'      => $job->jobName,
                                'companyName'  => $job->companyName,
                                'phoneNumber'  => $job->phone,
                                'jobLocation'  => $job->jobLocation
                                );
                }
                return response()->json(['status'=>1,'message'=>$msg1,'result'=>$jobArray]);
            }
            else
            {
                return response()->json(['status'=>0,'message'=>$msg2]);
            }        
              
        // }else
        // {
        //     return response()->json(['status'=>0,'message'=>$msg3]);
        // }

    }
   /// Send Request ///
    public function getJobDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //'userId'        => 'required',
           // 'userToken'     => 'required',
            'jobId'           => 'required',
            ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input      = $request->all();
      //  $userId     = $input['userId'];
        $jobId      = $input['jobId'];
    

        $msg1 = "Job Detail Found";
        $msg3 = "You are not authorised.Please login again.";
        $msg2 = "Job Detail Not Found";
    
      //  $verifiedUserId = Controller::checkToken($input['userId'], $input['userToken']);

        // if ($verifiedUserId == $userId) 
        // {        
                $JobDetails = Job::where('id', $jobId)
                                  ->where('isActive', 1)    
                                  ->first();
              $jovArray=[]; 
            if ($JobDetails) {
               
                $jobImage = asset('uploads/group.jpeg');
                    if(!empty($JobDetails->jobImage))
                    {
                        $jobImage = asset('uploads/jobImage/'.$JobDetails->jobImage);
                    }
                    $jobArray = array(
                        'jobId'        => $JobDetails->id,
                        'jobName'      => $JobDetails->jobName,
                        'companyName'  => $JobDetails->companyName,
                        'phoneNumber'  => $JobDetails->phone,
                        'jobLocation'  => $JobDetails->jobLocation,
                        'applyBefore'  => date('d F,Y',strtotime($JobDetails->applyBefore)),
                        'jobNature'    => $JobDetails->jobNature,
                        'salaryRange'  => $JobDetails->salaryRange,
                        'jobDescription'=> $JobDetails->jobDescription,
                        'rolesAndResponsibility'=> $JobDetails->roleAndResponcibility,
                        'postedOn'      => date('d F',strtotime($JobDetails->postDate)),
                        'jobImage'      =>$jobImage
                        );
                        return response()->json(['status'=>1,'message' => $msg1,'result'=> $jobArray]);
            } else {
              
                return response()->json(['status'=>1,'message' => $msg2]);
            }         
        // }
        // else{
        //     return response()->json(['status' => 0, 'message' => $msg3]);
        // }

    }
   
   /// Friend  Request Accept Reject///
   public function addJobApplication(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'userId'         => 'required',
           'userToken'      => 'required',
           'jobId'          => 'required',
           'firstName'      => 'required',
           'lastName'       => 'required',
           'birthdate'      => 'required',
           'age'            => 'required',
           'ssn'            => 'required',
           'city'           => 'required',
           'state'          => 'required',
           'zipcode'        => 'required',
           'phoneNumber'    => 'required',
           'educationLevel' => 'required',
           'uploadResume'   => 'required',
           'employmentHistory' => 'required',
           'languageKnown'     => 'required',
           'gender'           => 'required',
           'disability'       => 'required',
           'veteranStatus'    => 'required',
           'openToRelocation' => 'required',
           'signature'        => 'required',
           ]);

       if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()], 401);
       }

       $input      = $request->all();
       $id         = $input['userId'];
      
       
       $msg1 = "Form Submited";
       $msg3 = "You are not authorised. Please login again.";
       $msg2 = "Form not Submited";
     

       $verifiedUserId = Controller::checkToken($input['userId'], $input['userToken']);

       if ($verifiedUserId == $id) 
       {        

        if (isset($input["uploadResume"])) {
            $imagePath              = $input["uploadResume"];
            $extension              = pathinfo($imagePath->getClientOriginalName(), PATHINFO_EXTENSION);
            $filename               = time() . "uploadResume." . $extension;
            $input['uploadResume']  = $filename;
        }
        
        if (isset($input["signature"])) {
            $imagePath1              = $input["signature"];
            $extension              = pathinfo($imagePath1->getClientOriginalName(), PATHINFO_EXTENSION);
            $filename               = time() . "signature." . $extension;
            $input['signature']  = $filename;
        }
        
        $input['dob']=date('Y-m-d',strtotime($input['birthdate']));
        $input['phone']=$input['phoneNumber'];
               $userForm = UserRegistration::create($input);
   
           if ($userForm->id >0) {
            if (isset($input["uploadResume"])) {
                //$imagePath              = $input["uploadResume"];
                $upload_dir_path        = public_path() . "/uploads/uploadResume";
                $imagePath->move($upload_dir_path, $userForm->uploadResume);
            }
            if (isset($input["signature"])) {
              //  $imagePath1              = $input["signature"];
                $upload_dir_path        = public_path() . "/uploads/signature";
                $imagePath1->move($upload_dir_path, $userForm->signature);
            }              
                return response()->json(['status'=>1,'message' => $msg1]);
                                
           } else {
               
               return response()->json(['status'=>1,'message' => $msg2]);
           }         
       }
       else{
           return response()->json(['status' => 0, 'message' => $msg3]);
       }

   }
}

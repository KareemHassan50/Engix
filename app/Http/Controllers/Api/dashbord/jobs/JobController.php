<?php

namespace App\Http\Controllers\Api\dashbord\jobs;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    public function __construct()
    {
        if (Auth::guard('admins')->check()) {
            $this->middleware('auth:sanctum');
            $this->middleware('can:jobs');
        } else {
            // $this->middleware('auth:sanctum');
        }
    }
    public function index()
    {
        // dd('kareem');
        $jobs = Job::get();
        if ($jobs->count() == 0) {
            return response()->json([
                'status' => true,
                'message' => 'nothing Jobs now',
                // 'token' => $product->createToken("API TOKEN")->plainTextToken
            ], 200);
        }
        return response($jobs);
    }
    public function show($id)
    {
        $job = Job::findOrFail($id);
        if ($job->count() == 0) {
            return response()->json([
                'status' => true,
                'message' => 'nothing Jobs now',
                // 'token' => $product->createToken("API TOKEN")->plainTextToken
            ], 200);
        }
        return response($job);
    }
    public function create(Request $req)
    {
        $validatejob = Validator::make(
            $req->all(),
            [
                "nameAr" => 'required',
                "nameEn" => 'required',
                "image" => 'required|mimes:png,jpg',
                "locationAr" => 'required',
                "locationEn" => 'required',
                "requirmentEn" => 'required',
                "requirmentAr" => 'required',
                "descriptionEn" => 'required',
                "descriptionAr" => 'required',
                "whatYouWillDo" => 'required',
            ]
        );
        if ($validatejob->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validatejob->errors()
            ], 401);
        }
        $image = $req->image;
        if ($image) {
            $file = $req->file('image');
            $extention = $file->getClientOriginalExtension();
            $filename = "Image" .  time() . '.' . $extention;
            $path =  url('/images/job/' . $filename);
        }
        $OurClient = Job::create([
            "nameAr" => $req->nameAr,
            "nameEn" => $req->nameEn,
            "image" => $path,
            "locationAr" => $req->locationAr,
            "locationEn" => $req->locationEn,
            "requirmentEn" => $req->requirmentEn,
            "requirmentAr" => $req->requirmentAr,
            "descriptionEn" => $req->descriptionEn,
            "descriptionAr" => $req->descriptionAr,
            "whatYouWillDo" => $req->whatYouWillDo,
        ]);
        $file->move('images/job/', $filename);
        return response()->json([
            'status' => true,
            'message' => 'Job Create Successfully',
            // 'token' => $product->createToken("API TOKEN")->plainTextToken
        ], 200);
    }
    public function update(Request $req, $id)
    {
        $validatejob = Validator::make(
            $req->all(),
            [
                "nameAr" => 'required',
                "nameEn" => 'required',
                "image" => 'required|mimes:png,jpg',
                "locationAr" => 'required',
                "locationEn" => 'required',
                "requirmentEn" => 'required',
                "requirmentAr" => 'required',
                "descriptionEn" => 'required',
                "descriptionAr" => 'required',
                "whatYouWillDo" => 'required',
            ]
        );
        if ($validatejob->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validatejob->errors()
            ], 401);
        }
        $jobimage = Job::findOrFail($id);
        $name = $jobimage->image;
        $nameImageUpdate = ltrim($name, url('images/job'));
        $imagess = $req->file("image");
        if ($imagess) {
            if ($nameImageUpdate !== null) {
                unlink(public_path("images/job/") . $nameImageUpdate);
            }
            $image = $req->file("image");
            $nameOfNewImage = "Image" . time() . "." . $image->getClientOriginalExtension();
            $image->move(public_path("images/job/"), $nameOfNewImage);
            $path =  url('images/job/' . $nameOfNewImage);
        }
        $jobimage->update([
            "nameAr" => $req->nameAr,
            "nameEn" => $req->nameEn,
            "image" => $path,
            "locationAr" => $req->locationAr,
            "locationEn" => $req->locationEn,
            "requirmentEn" => $req->requirmentEn,
            "requirmentAr" => $req->requirmentAr,
            "descriptionEn" => $req->descriptionEn,
            "descriptionAr" => $req->descriptionAr,
            "whatYouWillDo" => $req->whatYouWillDo,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Job Updated Successfully',
            // 'token' => $product->createToken("API TOKEN")->plainTextToken
        ], 200);
    }
    public function destroy($id)
    {
        $jobDelete = Job::findOrFail($id);
        $imagePath =  $jobDelete->image;
        $nameImageUpdate = ltrim($imagePath, url('/images/job'));
        if ($nameImageUpdate) {
            unlink(public_path("images/job/") . $nameImageUpdate);
        }
        $jobDelete->delete();
        return response()->json([
            'status' => true,
            'message' => 'Job Deleted Successfully',
            // 'token' => $product->createToken("API TOKEN")->plainTextToken
        ], 200);
    }
}
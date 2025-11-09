<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    

    public function AdminLogout(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    //End Method 

    public function AdminProfile(){
        $id = Auth::user()->id;
        $adminData = User::find($id);
        return view('admin.admin_profile', compact('adminData'));
    }


    public function AdminProfileStore(Request $request){
        $id = Auth::user()->id;
        $data = User::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;

        $oldPhotoPath = $data->photo;

        if ($request->hasFile('photo')) {
           $file = $request->file('photo');
           $filename = time().'.'.$file->getClientOriginalExtension();
           $file->move(public_path('upload/admin_images'),$filename);
           $data->photo = $filename;
        }

        if ($oldPhotoPath && $oldPhotoPath !== $filename) {
            $this->deleteOldImage($oldPhotoPath);
        }

        $data->save();

        $notification = array(
            'message' => 'Admin Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

    }
     //End Method 
    private function deleteOldImage($oldPhotoPath){
        $oldImagePath = public_path('upload/admin_images/'.$oldPhotoPath);
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }   
    }

    //End Method

    public function AdminChangePassword(){

        return view('admin.admin_profile');

    }
      //End Method 

    public function AdminPasswordUpdate(Request $request){

        $user = Auth::user();
        $request->validate([
           'old_password' => 'required',
           'new_password' => 'required|confirmed'
        ]);
   
        if (!Hash::check($request->old_password, $user->password)) {
           
           $notification = array(
               'message' => 'Old Password Does not Match!',
               'alert-type' => 'error'
           ); 
           return back()->with($notification); 
        }
   
        User::whereId($user->id)->update([
           'password' => Hash::make($request->new_password) 
        ]);
   
        Auth::logout();
   
        $notification = array(
               'message' => 'Password Updated Successfully',
               'alert-type' => 'success'
           ); 
           return redirect()->route('login')->with($notification); 
   
       }
          //End Method 
}

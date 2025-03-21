<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\FileShare;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $userUploads = Upload::where('uploaded_by', $userId)->get();
        
        return view('dashboard', [
            'uploads' => $userUploads,
        ]);
    }
    public function store(Request $request)
    {
        $user = Auth::user()->id;

        $request->validate([
            // Allowed file types and size limit
            'file' => 'required|mimes:jpg,png,pdf,txt,doc,docx,xlsx,csv|max:2048', 
        ]);

        // Store the file in the storage folder
        $path = $request->file('file')->store('uploads', 'public');

        // Save file info in the database along with the user ID
        Upload::create([
            'uploaded_by' => $user,
            'filename' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }

    public function destroy(Request $request)
    {
         $upload_id = $request->get('customer_id');
         $upload = Upload::where('id', $upload_id)->first();
    
         if ( $upload ) {
             $upload->delete();
             return redirect()->back()->with('success', 'File deleted successfully.');
         } else {
            return redirect()->back()->with('error', 'File not found in the Database. ERROR IN THE DATABSE!');
         }
    }
}

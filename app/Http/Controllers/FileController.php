<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\FileShare;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        // User info
        $userId = Auth::id();
        $user_email = Auth::user()->email;

        // Gets all the users that is not the logged-in user for the sharing system
        $users = User::where('id', '!=', $userId)->get();

        // Gets all the files that the user uploads
        $user_uploads = Upload::where('uploaded_by', $userId)->get();
        
        // Gets all the files that are shared via email
        $shared_files = FileShare::where('shared_with_user_email', $user_email)->with('file')->get();
        
        // Counts the amount of files that the user has in the database linked to the user's logged-in id
        $user_file_count = Upload::where('uploaded_by', $userId)->count();
        
        // Set the max amount of storage for all users
        $max_storage_limit = 50;

        if ($max_storage_limit > 0) {
            $user_file_percentage = ($user_file_count / $max_storage_limit) * 100;
            $remaining_storage_percentage = 100 - $user_file_percentage;
        } else {
            $user_file_percentage = 0;
            $remaining_storage_percentage = 0;
        }

        // Prepare data for the pie chart
        $chart_data = [
            'labels' => ['Your Files', 'Available Storage'], 
            'data' => [$user_file_percentage, $remaining_storage_percentage]
        ];

        $fietyypedata = Upload::selectRaw('SUBSTRING_INDEX(filename, ".", -1) as file_type, COUNT(*) as count')->where('uploaded_by', $userId)->groupBy('file_type')->pluck('count', 'file_type')->toArray();
    
    if (empty($fietyypedata)) {
        $fietyypedata = ['default' => 0];
    }

        $file_type_labels = array_keys($fietyypedata); 
        $file_type_amount_per_file = array_values($fietyypedata);
        

        // Getitng the file per day per user uplaod with mayber averge?

        $files_by_date = Upload::selectRaw("DATE_FORMAT(created_at, '%d-%m') as date, COUNT(*) as count")->whereYear('created_at', 2024)->groupBy('date')->orderBy('date', 'asc')->pluck('count', 'date')->toArray();

        // Extract the labels (dates) and values (file counts)
        $upload_dates = array_keys($files_by_date);
        $upload_counts = array_values($files_by_date);


        // Getting all the files count but per year
        $files_by_year = Upload::selectRaw("DATE_FORMAT(created_at, '%Y') as date, COUNT(*) as count")->groupBy('date')->orderBy('date', 'asc')->pluck('count', 'date')->toArray();

        $upload_year = array_keys($files_by_year);
        $upload_year_files_amount = array_values($files_by_year);


        // Pass the data to the view
        return view('dashboard', [
            'email' => $user_email,
            'uploads' => $user_uploads,
            'sharedFiles' => $shared_files,
            'users' => $users,
            'data' => $chart_data,
            'fietyypelabels' => $file_type_labels,
            'fietyypedata' => $file_type_amount_per_file,
            'upload_dates' => $upload_dates,
            'upload_counts' => $upload_counts,
            'uplaod_file_year' => $upload_year,
            'upload_year_files_amount' => $upload_year_files_amount
        ]);
    }


     public function share(Request $request)
    {
        $request->validate([
            'file_id' => 'required|exists:uploads,id',
            'user_email' => 'required|email|exists:users,email'
        ]);

        $file = Upload::where('id', $request->input('file_id'))->where('uploaded_by', Auth::id())->first();

        if (!$file) {return back()->with('error', 'File not found or you do not have permission to share this file.');}

        $user = User::where('email', $request->input('user_email'))->first();

        FileShare::create([
            'file_id' => $file->id,
            'shared_with_user_email' => $user->email
        ]);

        return back()->with('success', 'File shared successfully.');
    }
}
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class UserController extends Controller
{
    public function index()
    {
        $users = DB::select('SELECT * FROM users');
        return view('user', ['users' => $users]);
    }

    public function destroy($id)
    {
        DB::delete('DELETE FROM users WHERE id = ?', [$id]);
        return redirect()->route('users.index')->with('success', 'User record deleted successfully.');
    }
}
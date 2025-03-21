<?php
namespace App\Http\Controllers;

use App\Services\DatabaseService;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DatabaseController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseService $databaseService){
        $this->databaseService = $databaseService;
    }

    public function index(): View {
        $tables = $this->databaseService->getTables();
        
        return view('database.index', ['tables' => $tables]);
    }

    public function reset_index(){
        return view('database.reset');
    }
    
    public function reset(Request $request): RedirectResponse {
        if($this->databaseService->reset()){
            $request->session()->flash('message', 'Reset success');
        }
        else{
            $request->session()->flash('message', 'Reset failed');
        }
        return redirect()->back();
    }

    public function import(Request $request){
        $this->validate($request, [
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $table_name= $request->get('table_name');
        if()
    }
}
?>

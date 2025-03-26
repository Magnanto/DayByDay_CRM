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

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'projectsFile' => 'required|file|mimes:csv,txt',
            'tasksFile' => 'required|file|mimes:csv,txt',
            'leadsFile' => 'required|file|mimes:csv,txt',
        ]);

        $projectsFile = $request->file('projectsFile');
        $tasksFile = $request->file('tasksFile');
        $leadsFile = $request->file('leadsFile');

        $message = $this->databaseService->import($projectsFile, $tasksFile, $leadsFile);

        if (is_array($message)) {
            $request->session()->flash('erreurs', $message);
        } else {
            $request->session()->flash('message', $message);
        }

        return redirect()->back();
    }
}
?>

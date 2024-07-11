<?php

namespace App\Http\Controllers;

use App\Models\ChainModel;
use App\Models\StageModel;
use App\Services\ChainServices;
use App\Services\FileServices;
use App\Services\StageServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChainController extends Controller
{
    private ChainServices $chainServices;

    private StageServices $stageServices;

    private FileServices $fileServices;
    public function __construct(ChainServices $chainServices, StageServices $stageServices, FileServices $fileServices) {
        $this->chainServices = $chainServices;
        $this->stageServices = $stageServices;
        $this->fileServices = $fileServices;
    }

    public function createChain(Request $request) {
        $title = $request->input('title');
        $webinarStartTime = $request->input('webinar_start_time');
        $webinarStartTime = json_decode($webinarStartTime);
        $stages = $request->input('stages');
        if(!$webinarStartTime) {
            $webinarStartTime = null;
        }

       

        $stages = array_map(function($stage, $index) use ($request) {
            if(!isset($stage['dayDispatch'])) {
                return [
                    'text' => $stage['text'],
                    'order' => $stage['order'],
                    'hour' => $stage['hour'],
                    'minute' => $stage['minute'],
                    'second' => $stage['second'],
                    
                ];
            }
            $path = null;
            if($request->hasFile('stages.'.$index.'.file')) {

                $file = $request->file('stages.'.$index.'.file');
                $path = $file->store('public');
                $path = $this->fileServices->generateLink($path);
            }
            return [
                'text' => $stage['text'],
                'dayDispatch' => $stage['dayDispatch'],
                'hour' => $stage['hour'],
                'minute' => $stage['minute'],
                'order' => $stage['order'],
                'file' => $path,
            ];
        }, $stages, array_keys($stages));
        $this->chainServices->createChain($title, $stages, $webinarStartTime);
        // return redirect()->route('chain');
    }

    public function getAll() {
        $chains = ChainModel::all();
        return view('chain/chain', ['chains' => $chains]);
    }

    public function deleteChain(Request $request, string $chainId) {
        DB::transaction(function () use ($chainId) {
            $chain = ChainModel::find($chainId);
            $chain->stages()->delete();
            $chain->delete();
            return redirect()->route('chain');
        });
    }

    public function getPageUpdate(string $id) {
        $chain = $this->chainServices->getChainById($id);
        $stages = $this->chainServices->getChainStages($id);
        return view('chain/update-chain', ['chain' => $chain,'stages' => $stages]);
    }

    public function updateChain(Request $request, string $chainId) {
        $title = $request->input('title');
        $webinarStartTime = $request->input('webinar_start_time');
        $webinarStartTime = json_decode($webinarStartTime);
        $stages = $request->input('stages');
        
        if(!$webinarStartTime) {
            $webinarStartTime = null;
        }

        $stages = array_map(function($stage, $index) use ($request) {
            if(!isset($stage['dateDispatch'])) {
                return [
                    'text' => $stage['text'],
                    'order' => $stage['order'],
                    'hour' => $stage['hour'],
                    'minute' => $stage['minute'],
                    'second' => $stage['second'],
                    
                ];
            }
            $path = null;
            if($request->hasFile('stages.'.$index.'.file')) {
                $file = $request->file('stages.'.$index.'.file');
                $path = $file->store('public');
                $path = $this->fileServices->generateLink($path);
            }
            return [
                'text' => $stage['text'],
                'dateDispatch' => $stage['dateDispatch'],
                'hour' => $stage['hour'],
                'minute' => $stage['minute'],
                'order' => $stage['order'],
                'file' => $path,
            ];
        }, $stages, array_keys($stages));
        $this->chainServices->updateChain($chainId, $title, $stages, $webinarStartTime);
        //return redirect()->route('chain');
    }
}
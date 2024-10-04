<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    public function index(Request $request){
        
        return view('leaderboard.index');
    }

    public function json(Request $request){

          // Log::info("message". print_r($request->all(),true));
          $page_index = (int)$request->input('start') > 0 ? ($request->input('start') / $request->input('length')) + 1 : 1;
  
          $limit = (int)$request->input('length') > 0 ? $request->input('length') : 10;
          $columnIndex = $request->input('order')[0]['column']; 
          $columnName = $request->input('columns')[$columnIndex]['data']; 
          $columnSortOrder = $request->input('order')[0]['dir']; 
          $filter = $request['options']['filter']; 
  
          $dateRange = $this->getDateRange($filter);
          $main_query =  User::with('activities')
                                ->whereHas('activities', function ($query) use ($dateRange) { 
                                    $query->whereBetween('performed_at', [$dateRange['start'], $dateRange['end']]);
                                })
                                ->select('users.id','users.full_name','users.total_points','users.rank')
                                ->orderBy($columnName, $columnSortOrder);

                      $recordsTotal = $main_query->count();
  
                      $recordsFiltered = $recordsTotal;
  
                      if(empty($request->input('search.value'))){
  
                          $data = $main_query->paginate($limit, ['*'], 'page', $page_index);
  
                      }else {
  
                          $search = $request->input('search.value');
                          $search_query = $main_query->where('users.id',$search);
  
                          $data = $search_query->paginate($limit, ['*'], 'page', $page_index);
 
                          $search_list_for_count = $search_query->get();  // group by and direct count not working
  
                          $recordsFiltered = count($search_list_for_count);
  
                      }
                      
                      $response = array(
                          "draw" => (int)$request->input('draw'),
                          "recordsTotal" => (int)$recordsTotal,
                          "recordsFiltered" => (int)$recordsFiltered,
                          "data" => $data->getCollection()
                      );
  
                    return response()->json($response, 200);
  
      }

    private function getDateRange($filter) {
        switch ($filter) {
            case 'day':
                return ['start' => Carbon::today(), 'end' => Carbon::now()];
            case 'month':
                return ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()];
            case 'year':
                return ['start' => Carbon::now()->startOfYear(), 'end' => Carbon::now()];
            default:
                return ['start' => Carbon::today(), 'end' => Carbon::now()];
        }
    }

    public function recalculate(){

        $users = User::all();
        foreach ($users as $user) {
            $totalPoints = $user->activities()->count() * 20;
            $user->update(['total_points' => $totalPoints]);
        }

        $this->recalculateRanks();
        return response()->json(['success' => true, 'message' => 'Leaderboard recalculated successfully.']);
    }

    private function recalculateRanks(){

        $users = User::orderBy('total_points', 'desc')->get();
        $rank = 0;
        $existPoint = null;

        foreach ($users as $key =>  $user) {

            if ($existPoint != $user->total_points) {
                $rank++;
            }
            $user->update(['rank' => $rank]);
            $existPoint  = $user->total_points;
           
        }
    }
}

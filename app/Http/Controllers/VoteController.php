<?php

namespace App\Http\Controllers;

use App\Vote;
use App\Response;
use App\VoteCriteria;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $votes = Vote::all();
        return view('votes.index',['votes'=>$votes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($gid)
    {
        // 0 for create and 1 for update
        return view('votes.create', ['type' => 0, 'gid' => $gid]);
    }

    public function addcriteria($vid)
    {
        // $criterias = VoteCriteria::all();
        $criterias = VoteCriteria::whereDoesntHave('vote', function($query) use ($vid) {
            $query->where('vote_id', $vid);
        })->get();
        // dd($criterias);
        return view('criteria.index', ['criterias'=>$criterias, 'vote_id'=>$vid]);
    }

    public function addvote(Request $request, $vid)
    {
        $criteria = VoteCriteria::find($request->criteria_id);

        $criteria->vote()->syncWithoutDetaching($vid); //prevent duplicate attahing

        return redirect()->action('VoteController@addcriteria', $vid)->with('status', 'Vote Added Successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'vote_name' => 'required|max:255',
            'question.*.criteria' => 'max:255',
        ]);

        $vote_data['vote_group_id'] = $request->vote_group;
        $vote_data['vote_name'] = $request->vote_name;
        $vote_data['user_id'] = Auth::id();

        // dd($vote_data);
        $vote = Vote::create($vote_data);

        $question_data = $request->question;

        foreach ($request->question as $key => $value) {

            if (is_null($question_data[$key]["criteria"])) {
                // criteria is null and not defined
                return redirect()->action('VoteController@addcriteria',['vid'=>$vote->id]);
            }
            $question_data[$key]['vote_id'] = $vote->id;
        }
        $quesiton = $vote->VoteCriteria()->createMany($question_data);

        return redirect()->action('VoteGroupController@show',['id'=>$request->vote_group])->with('status', 'Vote Created Successfully.');
    }

    /**
     * 显示 Vote 对象中全部的 Criteria 并进行评分
     *
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vote = Vote::find($id);
        $responses = Response::where('user_id', Auth::id())->where('vote_id', $id)->get();
        // IMPORTANT
        // This get() is a must to allow $responses to serve as iterators during foreach()
        // IMPORTANT
        return view('votes.vote', ['vote'=>$vote, 'responses'=>$responses]);
    }

    public function stat($id)
    {
        $vote = Vote::find($id);
        $responses = Response::where('vote_id', $id)->get();
        return view('votes.stat',['vote'=>$vote, 'responses'=>$responses]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function edit(Vote $vote)
    {
        $vote = Vote::find($id);
        return view('votes.create',['type' => 1, 'vote' => $vote]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vote $vote)
    {
        $this->validate($request, [
            'vote_name' => 'required|max:255',
            'question.*.question_content' => 'max:255',
        ]);

        $vote = Vote::find($id);
        if ($vote->user_id != Auth::id()) {
          return redirect('vote')->with('status', 'Permission Denied');
        }

        $vote->vote_name = $request->vote_name;
        $vote->save();
        if(!isset($request->question)){
          return redirect()->action('VoteController@edit',['id'=>$vote->id])->with('status', 'Question Cannot be null.');
        }

        foreach ($request->question as $question) {
          if (isset($question['id'])) {
            $q = Question::find($question['id']);
            $q->question_content = $question['question_content'];
            $q->save();
          }elseif (isset($question['question_content'])){
            $q = new Question(['question_content' => $question['question_content']]);
            $v = Vote::find($id);
            $v->questions()->save($q);
          }
        }
        return redirect()->action('VoteController@stat',['id'=>$vote->id])->with('status', 'Vote Modified Successfully.');
    }

    /**
     * 记录 respose
     *
     */
    public function submit(Request $request, $id)
    {
        $this->validate($request, [
            'answer.*.criteria' => 'required|max:255',
        ]);
        $data = $request->answer;
        // dd($data);
        foreach ($request->answer as $key => $value) {
            Response::updateOrCreate(['user_id' => Auth::id(), 'vote_id' => $id, 'vote_criteria_id' => $data[$key]['question_id']], ['response' => $data[$key]['criteria']]);
        }
        return back()->with('status', 'Response Submitted Successfully.');
    }

    public function export(Vote $vote)
    {
        // questions refers to the number of criterias
        $questions = $vote->votecriteria;
        $answered_users = Response::where('vote_id', $vote->id)->get()->unique($key = 'user_id', $strict = false);
        $answers = Response::where('vote_id', $vote->id)->get();

        // Table Header Starts
        $export_csv = "<table><tr><th>用户名</th>";
        foreach($questions as $question){
            // print all criterias
            $export_csv = $export_csv.'<th>';
            $export_csv = $export_csv.$question->criteria;
            $export_csv = $export_csv.'</th>';
        }
        $export_csv = $export_csv.'</tr>';
        // Table Header Ends

        foreach($answered_users as $answered_user){

            // Print Name of User
            $export_csv = $export_csv."<tr><td>".$answered_user->user->name.'</td>';

            $user_responses = $answers->where('user_id', $answered_user->user_id);
            foreach($user_responses as $user_response){
                // Print responce to each criteria
                $export_csv = $export_csv.'<td>';
                $export_csv = $export_csv.$user_response->response;
                $export_csv = $export_csv.'</td>';
            }
            $export_csv = $export_csv.'</tr>';
        }

      $export_csv = $export_csv.'</table>';

      echo $export_csv;
    }

    public function clearResponse($id)
    {

        $vote = Vote::find($id);
        if ($vote->user_id != Auth::id()) {
          return redirect('vote')->with('status', 'Permission Denied');
        }

        $questions = Vote::find($id)->questions()->get();
        $q_id = array();
        $ans_id = array();

        // dd($questions);

        if(isset($questions)){
          foreach ($questions as $question) {
            array_push($q_id, $question->id);
            $answers = Question::find($question->id)->answers()->get();
            if (isset($answers)) {
              foreach ($answers as $answer) {
                array_push($ans_id, $answer->id);
              }
            }

          }
        }

        Answer::destroy($ans_id);

        return redirect('/vote')->with('status', 'Responses Cleared Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vote = Vote::find($id);
      if ($vote->user_id != Auth::id()) {
        return redirect('vote')->with('status', 'Permission Denied');
      }

      $questions = Vote::find($id)->questions()->get();
      $q_id = array();
      $ans_id = array();

      // dd($questions);

        if(isset($questions)){
            foreach ($questions as $question) {
                array_push($q_id, $question->id);
                $answers = Question::find($question->id)->answers()->get();
                if (isset($answers)) {
                    foreach ($answers as $answer) {
                        array_push($ans_id, $answer->id);
                    }
                }
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\VoteGroup;
use App\Vote;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VoteGroupController extends Controller
{
    /**
     * 显示所有的votegroup
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vote_groups = VoteGroup::all();
        $votes = Vote::all();
        // dd($vote_groups, $votes);
        return view('votegroups.index', ['vote_groups' => $vote_groups, 'votes' => $votes]);
    }

    /**
     * 创建新的votegroup
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('votegroups.create',['type' => 0]);
    }

    /**
     * 存储新建的 votegroup
     * 同时重定向到新建 vote 对象的页面
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request,[
          'group_name' => 'required|max:255',
        ]);

        $group_data['group_name'] = $request->group_name;
        $group = VoteGroup::create($group_data);
        return redirect()->action(
            'VoteController@create', ['gid' => $group->id]
        )->with('status', 'Vote Group Created Successfully.');
    }

    /**
     * 显示 VoteGroup 中的所有 vote 对象
     *
     * @param  \App\VoteGroup  $voteGroup
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vote_group = VoteGroup::find($id);
        return view('votegroups.show', ['vote_group'=>$vote_group]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\VoteGroup  $voteGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(VoteGroup $voteGroup)
    {
        $vote_group = VoteGroup::find($id);
        return view('votegroups.show',['vote_group'=>$vote_group]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VoteGroup  $voteGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VoteGroup $voteGroup)
    {
        //
    }

    public function selectvote($id)
    {
      // dd(Vote::find(1)->has('vote_groups'));
      $votes = Vote::all();
      return view('votes.index',['votes'=>$votes,'group_id'=>$id]);
    }

    public function addvote(Request $request, $id)
    {
        //
        $vote = Vote::find($request->vote_id);
        $vote->vote_groups()->attach($id);
        return redirect()->action('VoteGroupController@selectvote',$id)->with('status', 'Vote Added Successfully.');
    }

    public function rmvote(Request $request, $id)
    {
        //
        $vote = Vote::find($request->vote_id);
        $vote->vote_groups()->detach($id);
        return redirect()->action('VoteGroupController@selectvote',$id)->with('status', 'Vote Removed Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VoteGroup  $voteGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(VoteGroup $voteGroup)
    {
        $vote_group = VoteGroup::find($id);
        $votes = $vote_group->votes->all();
        foreach ($votes as $vote) {
          $vote_group->votes()->detach($vote['id']);
        }
        VoteGroup::destroy($id);
        return redirect()->action('VoteGroupController@index')->with('status', 'Vote Group Removed Successfully.');
    }
}

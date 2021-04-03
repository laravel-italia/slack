<?php

use Illuminate\Support\Facades\Route;

Route::post('invite', function(\Vluzrmos\SlackApi\Contracts\SlackUserAdmin $slackUserAdmin, \Illuminate\Http\Request $request) {
    $invitation = $slackUserAdmin->invite($request->input('email'));
    if($invitation->ok === false) {
        $code = 'lines.errors.' . $invitation->error;
        $message = (trans($code) === $code) ? trans('lines.errors.generic') : trans($code);

        return view('error', compact('message'));
    }

    return view('success');
});

Route::get('/', function (\Vluzrmos\SlackApi\Contracts\SlackTeam $slackTeam, \Vluzrmos\SlackApi\Contracts\SlackApi $api) {
    $result = $slackTeam->info();
    if(!$result->ok) {
        return trans('lines.auth_error');
    }

    $teamName = $result->team->name;

    $channels = $api->get('conversations.list')->channels;
    $channels = array_filter($channels, function($channel){
        return ($channel->name === 'general') ? true : false;
    });
    $usersCount = reset($channels)->num_members;

    return view('welcome', compact('teamName', 'usersCount'));
});

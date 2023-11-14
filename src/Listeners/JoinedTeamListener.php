<?php

namespace Maosal\TeamworkPermission\Listeners;

use Mpociot\Teamwork\Events\UserJoinedTeam;

class JoinedTeamListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserJoinedTeam  $event
     * @return void
     */
    public function handle(UserJoinedTeam $event)
    {
        $user = $event->getUser();
        $team_id = $event->getTeamId();

        // get team role memeber id based on team id
        $team_role_id = \Maosal\TeamworkPermission\Models\TeamRole::where('team_id', $team_id)->where('name', 'Member')->first()->id;
        

        // Do something with the user and team ID.
        $user->updateTeamRole($team_role_id, $team_id);
    }
}
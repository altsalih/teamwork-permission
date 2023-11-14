<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleForTeamsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Roles Table
        Schema::create('team_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('label')->nullable();
            $table->timestamps();

            $table->foreign('team_id')
                ->references('id')->on('teams')
                ->onDelete('cascade');
        });

        // Add default roles
      

        // Add team role id to team user table
        Schema::table(\Config::get( 'teamwork.team_user_table' ), function (Blueprint $table) {
            $table->integer('team_role_id')->unsigned()->default(0);
        });

        // Add team owner's to role table and give default role
        App\Team::all()->each(function($team){
            $users = $team->users;

            $admin =  Maosal\TeamworkPermission\Models\TeamRole::create(['team_id'=>$team->id,'name'=>'Admin', 'label'=>'Administrator'])->id;
            $memeber = Maosal\TeamworkPermission\Models\TeamRole::create(['team_id'=>$team->id,'name'=>'Member', 'label'=>'Member'])->id;

            foreach($users as $user)
            {
                // Legacy isOwnerOfTeam($team)
                $team_model   = Config::get( 'teamwork.team_model' );
                $team_key_name = ( new $team_model() )->getKeyName();
                $isOwnerOfTeam = ( ( new $team_model )
                    ->where( "owner_id", "=", $user->getKey() )
                    ->where( $team_key_name, "=", $team->id )->first()
                ) ? true : false;

                // Check if owner
                if($isOwnerOfTeam)
                {
                    // Add Admin role to team's Owner
                    $user->changeTeamRole($admin, $team->id);
                }
                else
                {
                    // Add Member role to team's Member
                    $user->changeTeamRole($memeber, $team->id);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(\Config::get( 'teamwork.team_user_table' ), function ($table) {
            $table->dropColumn('team_role_id');
        });
        Schema::dropIfExists('team_roles');
    }
}
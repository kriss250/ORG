<?php

/**
 * UserGroup short summary.
 *
 * UserGroup description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\Frontdesk;

class UserGroup
{
     const ADMIN=1;
     const MANAGER=2;
     const FOMANAGER=3;
     const SUPERVISOR=4;
     const RECEPTIONIST = 5;
     const HOUSEKEEPER = 6;
     const VIEWER=7;

     public function belongsTo($group)
     {
         return \Auth::user()->group_id == $group;
     }
}
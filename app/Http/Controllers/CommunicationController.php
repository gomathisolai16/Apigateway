<?php

namespace App\Http\Controllers;

use App\Services\CommunicationService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommunicationController extends Controller
{
    use ApiResponser;

    /**
     *
     * @lrd:start
     *  To get notification lists
     * @lrd:end
     *
     * @QAparam is_read int 0-unread,1-read
     * @QAparam is_view_all int 0-list,1-read all
     * @QAparam type int 1-Update notification, 2-General notification
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function notificationList(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id,
        ]);
        $communicationService = new CommunicationService;
        return $communicationService->notificationList($request);
    }

    /**
     *
     * @lrd:start
     *  To get notification detail
     * @lrd:end
     *
     * @QAparam notification_id int required particular notification id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function notificationDetail(Request $request)
    {
        $request->request->add([
            'auth_user_id' => $request->user()->id,
        ]);
        $communicationService = new CommunicationService;
        return $communicationService->notificationDetail($request);
    }

    /**
     *
     * @lrd:start
     *  To get history details
     * @lrd:end
     *
     * @QAparam user_id int required particular user id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function historyDetails(Request $request)
    {
        $request->request->add([
            'entity_id' => $request->entity_id,
            'module' => $request->module,
        ]);
        $communicationService = new CommunicationService;
        return $communicationService->historyDetails($request);

    }

    /** Fetch history details for participants
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function participantHistoryDetails(Request $request)
    {
        $request->request->add([
            'entity_id' => $request->entity_id,
            'module' => $request->module,
        ]);
        $communicationService = new CommunicationService;
        return $communicationService->participantHistoryDetails($request);
    }
}

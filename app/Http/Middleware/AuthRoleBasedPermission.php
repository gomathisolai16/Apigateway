<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\RoleService;
use Illuminate\Http\Response;
use App\Traits\ApiResponser;
use App\Models\User;
class AuthRoleBasedPermission
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->route()->getName() != ""){
            $roleService = new RoleService;
            $requestAdmin = $request;
            $checkUser = User::findUserById($request->user()->id);
            if($checkUser && $checkUser->user_type !=1){
                if($checkUser->status == config('constant.STATUS_VALUE.ACTIVE')){
                    $requestAdmin->merge(['login_user_id' => $request->user()->id,'permission_path' => $request->route()->getName()]);
                    $response = json_decode($roleService->checkRolePermissions($requestAdmin));
                    if($response->status){
                        return $next($request);
                    }else{
                        return $this->response("Permission not found", Response::HTTP_UNAUTHORIZED,$response->data);
                    }
                }else{
                    return $this->response("User is inactive", Response::HTTP_UNAUTHORIZED,[]);
                }
            }else{
                return $next($request);
            }
        }
        return $next($request);
    }
}

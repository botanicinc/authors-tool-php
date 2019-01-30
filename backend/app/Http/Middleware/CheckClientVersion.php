<?php

namespace App\Http\Middleware;

use Closure;

class CheckClientVersion
{

    /**
     * Checks client version
     * Will reject any request from a client with a major or minor version change
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('X-BAT-Version')) {

            $package = json_decode(file_get_contents('../../frontend/package.json'));//@TODO cache this

            list($currentMajor, $currentMinor, $currentPatch) = explode('.', $package->version);
            list($clientMajor, $clientMinor, $clientPatch) = explode('.', $request->header('X-BAT-Version'));

            if ($clientMinor != $currentMinor || $clientMajor != $currentMajor) {
                return response()->json(['message' => 'CLIENT_VERSION_MISMATCH'], 426);
            }
        }
        return $next($request);
    }

}

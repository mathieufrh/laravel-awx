<?php

declare(strict_types=1);

/*
 * This is a Laravel wrapper for the oauth2-client.
 *
 * (c) Sdwru https://github.com/sdwru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdwru\Awx;

use Illuminate\Support\Facades\Cache;
use AwxV2\Oauth\Oauth2;

/**
 * This is the awx factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AwxOauthWrapper
{

    /**
     * Make a new oauth client.
     *
     * @param string[] $config
     *
     * @return \AwxV2\AwxV2
     */
    public function make(array $config)
    {
        return new Oauth2($config);
    }
    
    public function getAwxAccessToken(Oauth2 $oauth)
    {
        $provider = $oauth->awxProvider();
        
        // If tokens exists in cache storage, check if expired.
        // If not expired use it, otherwise refresh it.
        // If refresh fails generate new access token and replace it for the existing in cache storage
        if (Cache::has('tokens')) {

            $existingTokens = Cache::get('tokens');
            
            if ($existingTokens->hasExpired()) {
                $newAccessTokens = $provider->getAccessToken('refresh_token', [
                    'refresh_token' => $existingTokens->getRefreshToken()
                ]);

                if ($newAccessTokens === 'EXPIRED_REFRESH_TOKEN') {

                    // If refresh token is expired the only option left is to get new tokens using the resource owner password credentials grant.
                    $newPassGrantTokens = $oauth->passCredGrant();

                    // Purge old atokens and store new tokens to storage.
                    Cache::forever('tokens', $newPassGrantTokens);

                    return $newPassGrantTokens->getToken();
                     
                } else {
                    
                    // Purge old tokens and store refreshed tokens.
                    Cache::forever('tokens', $newAccessTokens);
 
                    return $newAccessTokens->getToken();
                }
            }

        // If tokens do not exist, generate them.
        } else {

            // Try get tokens using the resource owner password credentials grant.
            $getPassGrantTokens = $oauth->passCredGrant();

            // Store tokens.
            Cache::forever('tokens', $getPassGrantTokens);

            return $getPassGrantTokens->getToken();
        }
        
        // If we got this far the existing stored access token is still valid so return that.
        return $existingTokens->getToken();
    }
}

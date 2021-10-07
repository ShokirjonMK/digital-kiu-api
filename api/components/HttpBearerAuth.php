<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace api\components;

use yii\filters\auth\HttpBearerAuth as AuthHttpBearerAuth;
use yii\web\UnauthorizedHttpException;

class HttpBearerAuth extends AuthHttpBearerAuth
{

    /**
     * {@inheritdoc}
     */
    public function handleFailure($response)
    {
        throw new UnauthorizedHttpException('Your access token is invalid or expired.');
    }
    
}

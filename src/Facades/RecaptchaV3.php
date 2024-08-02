<?php
/**
 * Created by G-Systems
 * Date: 2024-08-02 11:02 AM
 * Mail: gsystems.it
 */

namespace GSystems\RecaptchaV3\Facades;


use Illuminate\Support\Facades\Facade;
use GSystems\RecaptchaV3\RecaptchaV3 as RecaptchaV3Instance;

class RecaptchaV3 extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return RecaptchaV3Instance::class;
    }
}

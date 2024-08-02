<?php
/**
 * Created by G-Systems
 * Date: 2024-08-02 11:52 AM
 * Mail: gsystems.it
 */


namespace GSystems\RecaptchaV3\Tests\Integration;



use GSystems\RecaptchaV3\Facades\RecaptchaV3;
use GSystems\RecaptchaV3\Tests\TestCase;



class VerifyTest extends TestCase
{



    public function testVerifyWithInvalidToken()
    {
        $this->mockRecaptchaResponse('invalid_token', '{
                  "success": false,
                  "error-codes": [
                    "invalid-input-secret"
                  ]
                }');

        $this->assertFalse(RecaptchaV3::verify('invalid_token'));

    }


    public function testVerifyWithValidToken()
    {
        $this->mockRecaptchaResponse('valid_token', '{
                  "success": true,
                  "score": 0.5,
                  "action": "my_action"
                }');

        $this->assertEquals(0.5, RecaptchaV3::verify('valid_token'));

    }



    public function testVerifiesAction()
    {
        $this->mockRecaptchaResponse('valid_token', '{
                  "success": true,
                  "score": 0.5,
                  "action": "my_action"
                }');

        $this->assertFalse(RecaptchaV3::verify('valid_token', 'other_action'));
        $this->assertEquals(0.5, RecaptchaV3::verify('valid_token', 'my_action'));

    }





}

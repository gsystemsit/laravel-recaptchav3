<?php
namespace GSystems\RecaptchaV3;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

class RecaptchaV3
{
    protected $secret;
    protected $sitekey;
    protected $origin;
    protected $locale;
    protected $http;
    protected $request;

    public function __construct(Repository $config, Client $client, Request $request, Application $app)
    {
        $this->secret = $config['recaptchav3']['secret'];
        $this->sitekey = $config['recaptchav3']['sitekey'];
        $this->origin = $config['recaptchav3']['origin'] ?? 'https://www.google.com/recaptcha';
        $this->locale = $config['recaptchav3']['locale'] ?? $app->getLocale();
        $this->http = $client;
        $this->request = $request;
    }

    public function verify($token, $action = null)
    {
        $response = $this->http->request('POST', $this->origin . '/recaptcha/api/siteverify', [
            'form_params' => [
                'secret'   => $this->secret,
                'response' => $token,
                'remoteip' => $this->request->getClientIp(),
            ],
        ]);

        $body = json_decode($response->getBody(), true);

        if (!isset($body['success']) || $body['success'] !== true) {
            return false;
        }

        if ($action && (!isset($body['action']) || $action != $body['action'])) {
            return false;
        }

        return $body['score'] ?? false;
    }

    public function sitekey()
    {
        return $this->sitekey;
    }

    public function initJs()
    {
        return '<script src="' . $this->origin . '/recaptcha/api.js?hl=' . $this->locale . '&render=' . $this->sitekey . '" async defer></script>';
    }

    public function field($action, $name = 'g-recaptcha-response')
    {
        $fieldId = uniqid($name . '-', false);
        $html = '<input type="hidden" name="' . $name . '" id="' . $fieldId . '">';
        $html .= "<script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof grecaptcha !== 'undefined') {
                    grecaptcha.ready(function() {
                        grecaptcha.execute('" . $this->sitekey . "', {action: '" . $action . "'}).then(function(token) {
                            document.getElementById('" . $fieldId . "').value = token;
                        });
                    });
                }
            });
        </script>";
        return $html;
    }
}
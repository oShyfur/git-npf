<?php

namespace Np\Structure\Classes\Oisf;

class AppLoginRequest
{
    private $ssoValues;
    private $cryptoUtil;
    private $nonce;

    function __construct()
    {
        $this->ssoValues = new SSOValues();
        $this->cryptoUtil = new CryptoUtil();
    }

    public function getReqNonce()
    {
        return $this->nonce;
    }

    public function buildRequest()
    {
        $requestUrl = $this->ssoValues->getIdpUrl() . "/" . $this->ssoValues->getAuthorizeEndPoint() . "?"; // $this->ssoValues->getAppNameQS() . "=" . $this->ssoValues->getAppName();
        $this->nonce = $this->cryptoUtil->getToken(10);

        $data = array(
            'response_type' => 'id_token',
            'response_mode' => 'form_post',
            'client_id' => $this->ssoValues->getAppId(),
            'scope' => 'openid',
            'redirect_uri' => $this->ssoValues->getRedirectUrl(),
            'landing_page_uri' => $this->ssoValues->getLandingPageUrl(),
            'state' => $this->cryptoUtil->getToken(10),
            'nonce' => $this->nonce
        );
        return $requestUrl . http_build_query($data);
    }
}

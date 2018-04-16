<?php

namespace BulkGate\Ssr;

use BulkGate,
    BulkGate\Ssr\Http,
    BulkGate\Utils;

class Client
{
    use BulkGate\Strict;

    /** @var array */
    private $server_data = [];

    /** @var array  */
    private $credentials = ["language" => "en"];

    /** @var string */
    private $domain;

    /** @var Http\IConnection */
    private $http_connection;

    /**
     * Client constructor.
     * @param $credentials
     * @throws Exception
     */
    public function __construct($credentials)
    {
        if
        (
            !isset($credentials["application_id"]) ||
            !isset($credentials["application_token"]) ||
            !isset($credentials["product"])
        )
        {
            throw new Exception("Some of required init fields is missing");
        }

        $this->credentials = $credentials;
        $this->http_connection = new Http\CurlConnection();

        $this->http_connection->setHeaders([
            "X-Requested-With" => "XMLHttpRequest",
            "X-BulkGate-Application-ID" => $this->credentials["application_id"],
            "X-BulkGate-Application-Token" => $this->credentials["application_token"],
            "X-BulkGate-Application-Product" => $this->credentials["product"],
            "X-BulkGate-Application-Language" => $this->credentials["language"],
        ]);
    }

    /**
     * @param string $url
     * @return string
     */
    private function createUrl($url)
    {
        return $this->domain . $url;
    }

    /**
     * @param string $domain
     * @param array $view
     * @param array $params
     * @return self
     * @throws Exception
     */
    public function load($domain, $view, $params)
    {
        $this->domain = $domain;
        $url = $this->createUrl("/ssr/load/".$view["presenter"]."/".$view["action"]);

        try
        {
            $this->server_data = Utils\Json::decode($this->http_connection->request($url, ["params" => $params])->getBody(), true);
        }
        catch(Http\HttpFailedResponse $e)
        {
            throw new Exception("Widget load failed");
        }
        catch(Utils\JsonException $e)
        {
            throw new Exception("JSON parsing from response body failed");
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getBootStrapScript()
    {
        $credentials = $this->credentials;

        $credentials["application_token"] = $this->server_data["client"]["token"];

        list ($credentials, $init) = [
            Utils\Json::encode($credentials),
            Utils\Json::encode($this->server_data["init"])
        ];

        return <<<EOT
<script type="text/javascript" src="{$this->createUrl("/dist/widget-api/widget-api.js")}"></script>
<script type="text/javascript">
_bg_client.initSSRWidget($credentials, $init);
</script>
EOT;
    }

    /**
     * @param $html_id string
     * @param $auto_generate_wrapper bool
     * @return string
     */
    public function getHtmlPart($html_id, $auto_generate_wrapper = true)
    {
        $html = $this->server_data["html"][$html_id] ?: "";

        return $auto_generate_wrapper ? "<div id=\"$html_id\">$html</div>" : $html;
    }

    /**
     * @param bool $auto_generate_wrapper
     */
    public function getHtml($auto_generate_wrapper = true)
    {
        $html = "";

        foreach($this->server_data["html"] as $html_id => $html_content)
        {
            $html .= $this->getHtmlPart($html_id, $auto_generate_wrapper);
        }
    }
}
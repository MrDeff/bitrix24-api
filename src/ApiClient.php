<?php
declare(strict_types=1);

namespace Bitrix24Api;

use Bitrix24Api\Batch\Batch;
use Bitrix24Api\Batch\Command;
use Bitrix24Api\Config\Config;
use Bitrix24Api\EntitiesServices\CRM\Company;
use Bitrix24Api\EntitiesServices\CRM\Contact;
use Bitrix24Api\EntitiesServices\CRM\Smart\Item;
use Bitrix24Api\EntitiesServices\Lists\Element as ListsElement;
use Bitrix24Api\EntitiesServices\Profile;
use Bitrix24Api\EntitiesServices\User;
use Generator;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    protected const BITRIX24_OAUTH_SERVER_URL = 'https://oauth.bitrix.info';
    protected const CLIENT_VERSION = '1.0.0';
    protected const CLIENT_USER_AGENT = 'bitrix24-api';
    protected Config $config;
    protected HttpClientInterface $httpClient;
    protected string $typeTransport = 'json';
    private $accessTokenRefreshCallback;


    public function __construct(Config $config = null)
    {
        $this->config = $config;
        $this->httpClient = HttpClient::create(['http_version' => '2.0']);
//        $traceableClient = new \Symfony\Component\HttpClient\TraceableHttpClient($this->httpClient);
//        $traceableClient->setLogger($this->log);
    }

    /**
     * Устанавливаем callback, который будет вызван при обновлении Credential'а
     * @param callable $callback
     * @return $this
     */
    public function onAccessTokenRefresh(callable $callback): self
    {
        $this->accessTokenRefreshCallback = $callback;

        return $this;
    }

    public function request(string $method, array $params = []): ?Response
    {
        if ($this->config->isWebHookMode()) {
            $url = sprintf('%s%s', $this->config->getWebHookUrl(), $method . '.' . $this->typeTransport);
        } else {
            $url = sprintf('%s%s', $this->config->getCredential()->getClientEndpoint(), $method . '.' . $this->typeTransport);
            if ($this->config->getCredential() === null) {

            }
            $params['auth'] = $this->config->getCredential()->getAccessToken();
        }

        $requestOptions = [
            'json' => $params,
            'headers' => $this->getRequestDefaultHeaders(),
        ];
        $response = null;
        $this->config->getLogger()?->debug(
            sprintf('request.start %s', $method),
            [
                'params' => $params,
            ]
        );
        try {
            $request = $this->httpClient->request('POST', $url, $requestOptions);
            $this->config->getLogger()?->debug(
                sprintf('request.end %s', $method),
                [
                    'httpStatus' => $request->getStatusCode(),
                    'body' => $request->toArray(false)
                ]
            );
            switch ($request->getStatusCode()) {
                case 200:
                    $response = new Response($request, new Command($method, $params));
                    break;
                case 404:
                    $body = $request->toArray(false);
                    if (isset($body['error'])) {
                        if ($body['error'] === 'ERROR_METHOD_NOT_FOUND') {
                            //todo: correct exception
                            throw new \Exception('ERROR_METHOD_NOT_FOUND');
                        }
                    }
                    break;
                case 400:
                    $body = $request->toArray(false);
                    if (isset($body['error'])) {
                        if ($body['error'] === 'ERROR_REQUIRED_PARAMETERS_MISSING') {
                            //todo: correct exception
                            throw new \Exception('ERROR_REQUIRED_PARAMETERS_MISSING:' . $body['error_description']);
                        }
                        else{
                            throw new \Exception('ERROR:'.$body['error'].' description:'. $body['error_description']);
                        }
                    }
                    break;
                case 401:
                    $body = $request->toArray(false);
                    if ($body['error'] === 'expired_token') {
                        $this->getNewAccessToken();
                        $response = $this->request($method, $params);
                    }
                    break;
                default:
                    $this->config->getLogger()?->debug(
                        sprintf('request.end %s', $method),
                        [
                            'httpStatus' => $request->getStatusCode(),
                            'body' => $request->toArray(false)
                        ]
                    );
                    break;
            }

        } catch (TransportExceptionInterface $e) {
            $this->config->getLogger()->error($e->getMessage());
        }

        return $response;
    }

    #[ArrayShape(['Accept' => "string", 'Accept-Charset' => "string", 'User-Agent' => "string"])] protected function getRequestDefaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Accept-Charset' => 'utf-8',
            'User-Agent' => sprintf('%s-v-%s-php-%s', self::CLIENT_USER_AGENT, self::CLIENT_VERSION, PHP_VERSION),
        ];
    }

    public function getNewAccessToken(): void
    {
        $url = sprintf(
            '%s/oauth/token/?%s',
            $this::BITRIX24_OAUTH_SERVER_URL,
            http_build_query(
                [
                    'grant_type' => 'refresh_token',
                    'client_id' => $this->config->getApplicationConfig()->getClientId(),
                    'client_secret' => $this->config->getApplicationConfig()->getClientSecret(),
                    'refresh_token' => $this->config->getCredential()->getRefreshToken(),
                ]
            )
        );

        $requestOptions = [
            'headers' => $this->getRequestDefaultHeaders(),
        ];
        try {
            $response = $this->httpClient->request('GET', $url, $requestOptions);
            switch ($response->getStatusCode()) {
                case 200:
                    $result = $response->toArray(false);

                    $this->config->getCredential()->setFromArray($result);
                    if (is_callable($this->accessTokenRefreshCallback)) {
                        $callback = $this->accessTokenRefreshCallback;
                        $callback($this->config->getCredential());
                    }
                    break;
                case 500:
                default:

                    break;
            }
        } catch (TransportExceptionInterface $e) {
            echo 'error';
            echo '<pre>';
            print_r($e);
            echo '</pre>';
        }
    }

    public function getList(string $method, array $params = []): Generator
    {
        do {
            $result = $this->request(
                $method,
                $params
            );

            $start = $params['start'] ?? 0;
            $this->config->getLogger()?->debug(
                "По запросу (getList) {$method} (start: {$start}) получено сущностей: " . count($result->getResponseData()->getResult()->getResultData()) .
                ", всего существует: " . $result->getResponseData()->getPagination()->getTotal(),
            );

            yield $result;

            if (empty($result->getResponseData()->getPagination()->getNextItem())) {
                break;
            }

            $params['start'] = $result->getResponseData()->getPagination()->getNextItem();
        } while (true);
    }

    public function getListFast(string $method, array $params = []): Generator
    {
        $params['order']['id'] = 'ASC';
        $params['filter']['>id'] = 0;
        $params['start'] = -1;

        $totalCounter = 0;

        do {
            $result = $this->request(
                $method,
                $params
            );

            $start = $params['start'] ?? 0;
            $resultCounter = count($result->getResponseData()->getResult()->getResultData());
            $totalCounter += $resultCounter;
            $this->config->getLogger()?->debug(
                "По запросу (getListFast) {$method} (start: {$start}) получено сущностей: " . $resultCounter .
                ", всего получено: " . $totalCounter,
            );

            yield $result;

            if ($resultCounter < 50) {
                break;
            }

            $params['filter']['>ID'] = $result->getResponseData()->getResult()->getResultData()[$resultCounter - 1]['ID'];
        } while (true);
    }

    #[Pure] public function profile(): Profile
    {
        return new Profile($this);
    }

    #[Pure] public function CrmSmartItem(): Item
    {
        return new Item($this);
    }

    #[Pure] public function getLogger(): ?\Psr\Log\LoggerInterface
    {
        return $this->config->getLogger() ?? null;
    }

    #[Pure] public function listsElement(array $params = []): ListsElement
    {
        return new ListsElement($this, $params);
    }

    #[Pure] public function user(array $params = []): User
    {
        return new User($this, $params);
    }

    #[Pure] public function company(array $params = []): Company
    {
        return new Company($this, $params);
    }

    #[Pure] public function contact(array $params = []): Contact
    {
        return new Contact($this, $params);
    }

    #[Pure] public function batch(?bool $halt = false): Batch
    {
        return new Batch($this, $halt);
    }
}

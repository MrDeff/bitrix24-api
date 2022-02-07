<?php

namespace Bitrix24Api;

use Symfony\Contracts\HttpClient\ResponseInterface;

class Response
{
    protected ResponseInterface $httpResponse;
    private ?DTO\ResponseData $responseData;

    public function __construct(ResponseInterface $httpResponse)
    {
        $this->httpResponse = $httpResponse;
        $this->responseData = null;
    }

    public function getResponseData(): ?DTO\ResponseData
    {
        if ($this->responseData === null) {
            try {
                $responseResult = $this->httpResponse->toArray(true);
                if (!is_array($responseResult['result'])) {
                    $responseResult['result'] = [$responseResult['result']];
                }

                $nextItem = null;
                $total = null;
                if (array_key_exists('next', $responseResult)) {
                    $nextItem = (int)$responseResult['next'];
                }
                if (array_key_exists('total', $responseResult)) {
                    $total = (int)$responseResult['total'];
                }
                $this->responseData = new DTO\ResponseData(
                    new DTO\Result($responseResult['result']),
                    DTO\Time::initFromResponse($responseResult['time']),
                    new DTO\Pagination($nextItem, $total)
                );
            } catch (\Exception $exception) {

            }
        }
        return $this->responseData;
    }
}
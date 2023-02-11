<?php
/**
 * Created by PhpStorm.
 * User: YYZ
 * Date: 2023/2/10 21:49
 * Desc:
 */

namespace Zhaohui1909\Weather;


use GuzzleHttp\Client;
use http\Exception\InvalidArgumentException;
use Zhaohui1909\Weather\Exceptions\HttpException;

class Weather
{
    protected string $key;
    protected array $guzzleOptions = [];

    /**
     * Weather constructor.
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setHttpClient(array $options): void
    {
        $this->guzzleOptions = $options;
    }

    public function getWeather($city, string $type = 'base', string $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        if (!\in_array(strtolower($format), ['json', 'xml'], true)) {
            throw new InvalidArgumentException('Invalid response format');
        }

        if (!\in_array(strtolower($type), ['base', 'all'], true)) {
            throw new InvalidArgumentException('Invalid type value(base/all)');
        }

        $query = array_filter(
            [
                'key' => $this->key,
                'city' => $city,
                'output' => $format,
                'extensions' => $type,
            ]
        );

        try {
            $response = $this->getHttpClient()->get($url, ['query' => $query])->getBody()->getContents();

            return 'json' === $format ? json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
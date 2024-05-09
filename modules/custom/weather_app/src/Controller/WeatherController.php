<?php

namespace Drupal\weather_app\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Weather controller.
 */
class WeatherController extends ControllerBase {

  protected $httpClient;

  /**
   * Constructs a WeatherController object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   */
  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }

  /**
   * Weather block content callback.
   */
  public function weatherBlock() {
    $response = $this->httpClient->get('https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/istanbul?unitGroup=metric&key=7DXVW598F4WMCJDSWLL8H8P22&contentType=json');
    $data = json_decode($response->getBody(), true);
    $currentDate = new \DateTime();
    $formattedDate = $currentDate->format('Y-m-d');

    foreach ($data['days'] as $key => $value) {
        if ($value['datetime'] == $formattedDate) {
            $temperatureMax = $value['tempmax'];
            $temperatureMin = $value['tempmin'];
            $humidity       = $value['humidity'];
            $snow           = $value['snow'];
            $sunrise        = $value['sunrise'];
            $sunset         = $value['sunset'];
            $cloudcover     = $value['cloudcover'];
        }
    }

    return [
      '#theme' => 'weather-block',
      '#temperatureMax' => $temperatureMax,
      '#temperatureMin' => $temperatureMin,
      '#humidity'       => $humidity,
      '#snow'           => $snow,
      '#sunrise'        => $sunrise,
      '#sunset'         => $sunset,
      '#cloudcover'     => $cloudcover,
    ];
  }
}

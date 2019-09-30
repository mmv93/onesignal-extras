<?php

namespace Drupal\onesignal_extras;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Class Sender.
 */
class Sender {

  /**
   * REST API url.
   */
  const API_URL = 'https://onesignal.com/api/v1/notificationss';

  /**
   * Machine name for title field.
   */
  const TITLE = 'os_title';

  /**
   * Machine name for message field.
   */
  const MESSAGE = 'os_message';

  /**
   * Machine name for image field.
   */
  const IMAGE = 'os_image';

  /**
   * The config manager service.
   *
   * @var \Drupal\onesignal\Config\ConfigManagerInterface
   */
  private $configFactory;

  /**
   * The module configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * OneSignal REST api key.
   */
  protected $restApiKey;

  /**
   * OneSignal app id.
   */
  protected $appId;

  /**
   * Constructs a new MondaPromotionValidator object.
   */
  public function __construct(ConfigFactory $configFactory, EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack) {
    $this->configFactory = $configFactory;
    $this->entityTypeManager = $entity_type_manager;
    $this->requestStack = $request_stack;
    $this->config = $this->configFactory->get('onesignal_extras.settings');
    $os_config = $this->configFactory->get('onesignal.config');
    $this->appId = $os_config->get('onesignal_rest_api_key');
    $this->restApiKey = $os_config->get('onesignal_app_id');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('request_stack')
    );
  }

  /**
   * Sends push notification to subscribed users.
   *
   * @param \Drupal\Core\Entity\ContentEntityBase $entity
   *   Entity instance.
   */
  public function send(ContentEntityBase $entity) {
    $config = $this->configFactory->get('onesignal.config');
    $rest_api_key = $config->get('onesignal_rest_api_key');
    $app_id = $config->get('onesignal_app_id');
    $data = $this->prepareData($entity);

    $fields = [
      'app_id' => $app_id,
      'included_segments' => [
        'All',
      ],
      'contents' => ['en' => html_entity_decode($data['body'])],
      'headings' => ['en' => $data['title']],
      'chrome_web_image' => $data['chrome_big_picture'],
      'chrome_big_picture' => $data['chrome_big_picture'],
      'url' => $data['node_url'],
      'delayed_option' => 'last-active',
    ];
    if (isset($data['send_after'])) {
      $fields['send_after'] = $data['send_after'];
    }

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::API_URL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json; charset=utf-8',
      'Authorization: Basic ' . $rest_api_key,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    //curl_exec($ch);
    //curl_close($ch);
  }

  /**
   * Prepare data for notification.
   *
   * @param \Drupal\Core\Entity\ContentEntityBase $entity
   *   Entity instance.
   *
   * @return array
   *   Array with data.
   */
  private function prepareData(ContentEntityBase $entity) {
    $data = [
      'title' => $entity->get(self::TITLE)->getString(),
      'message' => $this->prepareMessage($entity->get(self::MESSAGE)->getString()),
      'url' => $entity->toUrl('canonical', ['absolute' => TRUE])->toString(),
    ];

    if ($icon_fid = $this->config->get('icon')) {
      $icon = File::load($icon_fid);
      $data['icon'] = $icon->createFileUrl(FALSE);
    }

    $image_fid = $entity->{self::IMAGE}->referencedEntities();
    if ($image = reset($image_fid)) {
      if ($image instanceof File) {
        $data['chrome_big_picture'] = $image->createFileUrl(FALSE);
      }
    }

    return $data;
  }

  /**
   * @param string $message
   *
   * @return string
   */
  protected function prepareMessage(string $message) {
    // Allows to send notification with empty message.
    if ($message == '') {
      $message = '&nbsp;&nbsp;';
    }

    return $message;
  }

}

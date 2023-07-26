<?php

namespace Drupal\custom_entity\EventSubscriber;

use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class to Subscribe the Node View Enent, ie whenever the nodeis viewed this
 * event shout be it.
 */
class MovieBudgetView implements EventSubscriberInterface {

  use \Drupal\Core\StringTranslation\StringTranslationTrait;

  /**
   * Stores the ConfigFactory Object to deal with different configuration
   * accross the site.
   * 
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs the Confif Object.
   * 
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   Contains the ConfigFactory object.
   */
  public function __construct(ConfigFactory $config) {
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::VIEW][] = ['onMovieNodeView', 2];
    return $events;
  }

  /**
   * Function to run when a node is viewed.
   * 
   * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
   *   Contains the ViewEvent object that allows to create a response for the
   *   return value of a controller.
   * 
   * @return void
   *   Returns void.
   */
  public function onMovieNodeView(ViewEvent $event) {
    $request = $event->getRequest();
    $node = $event->getControllerResult();
    if (!is_null($request->attributes->get('node'))) {
      $node_type = $request->attributes->get('node')->getType();

      if ($node_type == 'movie') {
        $movie_price = $request->attributes->get('node')->field_movie_price->value;
        $movie_budget = $this->config->get('custom_entity.budget')->get('budget');
        $message = $this->getMessage($movie_budget, $movie_price);
        $node['movie_price'] = [
          '#type' => 'markup',
          '#markup' => $this->t($message),
          '#weight' => -1,
          '#cache' => [
            'tags' => ['movie_budget'],
          ],
        ];
        $event->setControllerResult($node);
      }
    }
  }

  /**
   * Function to get the message to be displayed on the Movie entity.
   * 
   * @param int|null $movie_budget
   *   Contains the Movie Budget amount.
   * @param int $movie_price
   *   Contains the Actual Movie Price.
   * 
   * @return string
   *   Returns the message based on price Comparision.
   */
  public function getMessage(mixed $movie_budget, int $movie_price) {
    if ($movie_budget > $movie_price) {
      return 'Movie is under Budget';
    }
    else if ($movie_budget < $movie_price) {
      return 'Movie is over Budget';
    }
    else {
      return 'Movie Fits in Budget';
    }
  }

}

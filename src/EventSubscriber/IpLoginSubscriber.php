<?php

namespace Drupal\ip_login\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * IP Login subscriber.
 */
class IpLoginSubscriber implements EventSubscriberInterface {

  /**
   * Clears various IP Login cookies if needed.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The response event.
   */
  public function onKernelResponse(FilterResponseEvent $event) {
    if (!$event->isMasterRequest()) {
      return;
    }

    $response = $event->getResponse();
    if ($event->getRequest()->attributes->get('ip_login_user_login')) {
      $response->headers->setCookie(new Cookie('ipLoginAttempted', '', 1));
      $response->headers->setCookie(new Cookie('ipLoginAsDifferentUser', '', 1));
    }

    $can_login_as_another_user = $event->getRequest()->attributes->get('ip_login_can_login_as_another_user');
    if ($can_login_as_another_user !== NULL) {
      $response->headers->setCookie(new Cookie('ipLoginAsDifferentUser', $can_login_as_another_user));
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['onKernelResponse', 0];

    return $events;
  }

}

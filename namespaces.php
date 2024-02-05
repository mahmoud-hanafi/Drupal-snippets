<?php

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Controller\ControllerBase;
// entity
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\webform\Entity\WebformSubmission;
// response and redirect
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use Drupal\Core\Routing\RouteMatchInterface;
// generate token after installition of jwt module
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// database
use Drupal\Core\Database\Database;
// html
use Drupal\Component\Utility\Html;
use Drupal\Component\Render\FormattableMarkup;
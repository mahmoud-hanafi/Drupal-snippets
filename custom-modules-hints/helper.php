<?php 

// get allowed field options
$node->filed_name->getSetting('allowed_values')[$node->filed_name->value];

// get nid from url
$node = \Drupal::routeMatch()->getParameter('node');

// check if nid from url refer to node
$node = \Drupal::routeMatch()->getParameter('node');
if ($node instanceof \Drupal\node\NodeInterface) {
  // It's a node!
}

// **************************************************************** //

// Get Entity of field reference
use Drupal\node\Entity\Node;
$node = Node::load(xxx);
$entity = $node->get('FIELD_NAME')->referencedEntities();


// **************************************************************** //
// Get user id from url
$user = \Drupal::routeMatch()->getParameter('user');
$uid = $user->id();


// **************************************************************** //
// Make the field to be unique programmatically
use Drupal\Core\Entity\EntityTypeInterface;

// if you make a field belongs to a node just change $entity_type to 'node'

function hook_entity_bundle_field_info_alter(&$fields, EntityTypeInterface $entity_type, $bundle) {
  // D8 => $entity_type->id()
  // D9 => $entity->getEntityTypeId()
  if ($entity_type->id() === 'user') {
    if (isset($fields['field_user_id'])) {
      $fields['field_user_id']->addConstraint('UniqueField', ['message' => t('this id is used before with another user')]);
    }
  }
}

// **************************************************************** //

// get allowed field options
$node->filed_name->getSetting('allowed_values')[$node->filed_name->value];

// **************************************************************** //
// Get list item text label
// You can use multi ways to get list item text labels here are some :

use \Drupal\field\Entity\FieldConfig;

// FieldConfig::load('ENTITY.CONTENT_TYPE.FIELD_NAME')
$key = $node->get('field_type')->value;
$allowed_values = FieldConfig::load('node.news.field_type')->getFieldStorageDefinition()->getSettings()['allowed_values'];
$label = $allowed_values[$key];

//or directly :

$node->get('field_type')->view()[0]['#markup'];

// **************************************************************** //

// Get Moderation State label (Workflow)
// Put your Workflow ID here instead of MY_WORKFLOW_ID
$workflow = \Drupal::config("workflows.workflow.MY_WORKFLOW_ID");

// This will return a machine name of state EX: draft
$state = $node->get('moderation_state')->getString();

// Output will be : Draft.
$state_label = $workflow->get("type_settings.states.{$state}.label");

// **************************************************************** //
// Delete and Add custom message

// Delete all message by system
  \Drupal::messenger()->deleteAll();

// Adds a new warning message to the queue
\Drupal::messenger()->addWarning('Sorry! , this is a Warning message ', TRUE);

// Adds a new status message to the queue
\Drupal::messenger()->addStatus('Sorry! , this is a Success message ', TRUE);

//Adds a new error message to the queue.
\Drupal::messenger()->addError('Sorry! , this is a Error message ', TRUE);

// **************************************************************** //

// Render your images with image styles
//This would render the image tag with the image having been processed by the style.

$render = [
  '#theme' => 'image_style',
  '#style_name' => 'thumbnail',
  '#uri' => 'public://my-image.png',
  // optional parameters
];

// if we just want the URL of an image with the image style applied, we need to load the image style config entity and ask it for the URL:

use Drupal\image\Entity\ImageStyle;

$image_style = ImageStyle::load('550x300');
$image = $image_style->buildUri(FILE_URI);

// **************************************************************** //
// Add class to body programmatically
// In `.theme` file you can add custom classes to the body using `hook_preprocess_html` 

/**
 * Implements hook_preprocess_html()
 */
function hook_preprocess_html(&$variables)
{
  $route_name = \Drupal::request()->attributes->get('_route');
  if ($route_name == 'user.login') {
    $variables['attributes']['class'][] = 'page-custom-login';
  }
}

// **************************************************************** //
// Set start date and end date progmaticlly

use Drupal\node\Entity\Node;

$node = Node::load(NODE_ID);
$field_date = [
'value' => '2022-07-17',
'end_value' => '2022-10-01',
];

$node->set('field_date',$field_date);
$node->save();

// **************************************************************** //
// Get webform fields element programmatically
use Drupal\webform\Entity\Webform;
use Drupal\Core\Serialization\Yaml;

$webform = Webform::load('YOUR_WEBFORM_ID');
$elements = Yaml::decode($webform->get('elements'));


// **************************************************************** //
//  element validate and login by any another field
if ($form_id === 'user_pass' || $form_id === 'user_login_form') {
    $form['name']['#element_validate'][] = 'mobile_user_login_validate';
}
/**
 * Allow login by phone number.
 */
function mobile_user_login_validate($form, FormStateInterface $form_state) {
    // Use $form_state->getUserInput() in the error message to guarantee
    // that we send exactly what the user typed in.
    $user_input = $form_state->getUserInput();
    $name_input = $user_input['name'];
    if (!(\Drupal::service('email.validator')->isValid($name_input))) {
      if (!($name_input)) {
        $form_state->setErrorByName($form['name'], t('The phone number is not valid.'));
        return;
      } 
      //Try loading by phone number.
      if ($user = \Drupal::service("code_helper.user")->getUserByMobile($name_input, 1)) {
        //Set the username for further validation.
        $form_state->setValue('name', $user->data->getEmail());
        return FALSE;
      }
    
      // Set error message.
      $query = isset($name_input) ? ['name' => $name_input] : [];
      $form_state->setErrorByName(
        'name', t('Unrecognized email or password. <a href=":password">Forgot your password?</a>',
          [
            ':password' => URL::fromRoute('user.pass', [], ['query' => $query])->toString(),
          ]));
      $form_state->setRebuild();
      \Drupal::logger('mobile_user_login_validate')->notice('User fetched by mobile: ' . print_r($user, TRUE));
    }
}


// **************************************************************** //
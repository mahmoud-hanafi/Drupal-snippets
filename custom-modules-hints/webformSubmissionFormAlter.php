// First, load the webform from $form_state object 

$webform_submission = $form_state->getFormObject()->getEntity();

// to get the value of all elements

$values = $webform_submission->getData();

// after loading all elements values in an array you can get the value of one element using:

values['element_machine_name'];

// to get webform id

$webform_id = $webform_submission->get('webform_id')->target_id;

// to get the author

$webform_author_id = $webform_submission->get('uid')->target_id;

// to get the submission result of this form you first should get webform sid

$sid = $webform_submission->id(); 

$submission = WebformSubmission::load($sid);

$url = $submission->toUrl()->toString();
<?php

// 1. Define the route
// my_module.routing.yml

// my_module.views_ajax:
//   path: '/admin/views/{nojs}/{view_id}/{display_id}/{args}'
//   defaults:
//     _controller: '\Drupal\my_module\Controller\MyModuleViewAjaxController::ajaxView'
//   requirements:
//     _permission: 'access content'


// 2. Create the controller
// src/Controller/MyModuleViewAjaxController.php
    
namespace Drupal\my_module\Controller;
    
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\AppendCommand;
use Drupal\views\Controller\ViewAjaxController;
use Symfony\Component\HttpFoundation\Request;
    
/**
* Class MyModuleViewAjaxController
*/
class MyModuleViewAjaxController extends ViewAjaxController {
    public function ajaxView(Request $request) {
        $view_name = $request->get('view_id');
        $display_id = $request->get('display_id');
        $dom_id = "{$view_name}__{$display_id}";
    
        $request->request->set('view_name', $view_name);
        $request->request->set('view_display_id', $display_id);
        $request->request->set('view_args', $request->get('args'));
        $request->request->set('view_dom_id', $dom_id);
    
        return parent::ajaxView($request);
    }
}

// 3. Create a wrapper <div> to embed the view
// The <div> where you want to load a view via ajax has to have a specific css class in form of: .js-view-dom-id-{view_id}__{display_id}. Here replace the placeholders for view and display ids with the actual ones you want to embed.

// <div class="js-view-dom-id-{view_id}__{display_id}"></div>

// 4. Load the view via ajax link
// Now you can load the view creating links like:

// <a href="/admin/views/ajax/{view_id}/{display_id}/{args}" class="use-ajax">
//     This link will load the view {view_id} display {display_id} with the
//     arguments {args}
// </a>
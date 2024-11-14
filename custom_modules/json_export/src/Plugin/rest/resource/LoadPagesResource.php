<?php

namespace Drupal\json_export\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\node\Entity\Node;
use \Drupal\Component\Utility\Unicode;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "load_pages_resource",
 *   label = @Translation("List pages resource"),
 *   uri_paths = {
 *     "canonical" = "/rest-api/pages"
 *   }
 * )
 */
class LoadPagesResource extends ResourceBase
{

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
    protected $currentUser;

    /**
     * Constructs a new LoadPagesResource object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param array $serializer_formats
     *   The available serialization formats.
     * @param \Psr\Log\LoggerInterface $logger
     *   A logger instance.
     * @param \Drupal\Core\Session\AccountProxyInterface $current_user
     *   A current user instance.
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        array $serializer_formats,
        LoggerInterface $logger,
        AccountProxyInterface $current_user
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

        $this->currentUser = $current_user;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->getParameter('serializer.formats'),
            $container->get('logger.factory')->get('json_export'),
            $container->get('current_user')
        );
    }


    /**
     * Responds to GET requests.
     *
     * @param string $type
     *   The type of the entity which is node, user, term or vocabulary.
     * @param integer $id
     *   The id if the entity to be loaded.
     *
     * @return \Drupal\rest\ResourceResponse
     *   The HTTP response object.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function get()
    {
        $nids = \Drupal::entityQuery('node')
            ->accessCheck(TRUE)
            ->condition('type', 'pages')
            ->condition('status', 1)
            ->sort('nid', 'DESC')
            ->execute();
        $pages = Node::loadMultiple($nids);
        $r_pages = [];
        foreach ($pages as $page) {
            $page_id = $page->id();
            $page_title = $page->getTitle();
            $page_desc = strip_tags($page->get('body')->value);
            // get data from post loader paragrah type
            $post_loaders = $page->get('field_posts_loader')->referencedEntities();
            $r_post_loader = [];
            foreach ($post_loaders as $post_loader) {
                $post_loader_title = $post_loader->get('field_posts_loader_title')->getString();
                $post_loader_desc = strip_tags($post_loader->get('field_posts_loader_description')->value);
                // load avialable posts
                $r_posts = [];
                $posts = $post_loader->get('field_posts')->referencedEntities();
                foreach ($posts as $post){
                    $post_id = $post->id();
                    $post_title = $post->getTitle();
                    $post_desc = strip_tags($post->get('body')->value);
                    // get available gallery in posts
                    $galleries = $post->get('field_image_gallary')->referencedEntities();
                    // dd(count($galleries));
                    $r_galleries = [];
                    foreach ($galleries as $gallery) {
                        $gallery_title = $gallery->get('field_gallery_title')->getString();
                        $gallery_desc = strip_tags($gallery->get('field_gallery_description')->value);
                        //load all gallary images
                        $images = $gallery->get('field_image_gallary')->referencedEntities();
                        $r_images = [];
                        foreach ($images as $image) {
                            $image_title = $image->get('field_image_title')->getString();
                            $image_link_cta = $image->get('field_image_link_cta')->getString();
                            $image_alignment = $image->get('field_image_alignment')->getString();
                            $url_generator = \Drupal::service('file_url_generator');
                            $image_src = $url_generator->generateAbsoluteString($image->get('field_media_image')->entity->getFileUri());
                            $r_images[] = [
                                'image_title' => $image_title,
                                'image_link_cta' => $image_link_cta,
                                'image_alignment' => $image_alignment,
                                'image_src' => $image_src,
                            ];
                        }
                        $r_galleries[] = [
                            'gallery_title' => $gallery_title,
                            'gallery_description' => $gallery_desc,
                            'gallery_images' => $r_images,
                        ];
                    }
                    $r_posts[] = [
                        'post_id' => $post_id,
                        'post_title' => $post_title,
                        'post_description' => $post_desc,
                        'galleries' => $r_galleries,
                    ];
                }
                $r_post_loader[] = [
                    'post_loader_tiltle' => $post_loader_title,
                    'post_loader_description' => $post_loader_desc,
                    'posts' => $r_posts,
                ];  
            }  
            $r_pages[] = [
                'page_id' => $page_id,
                'page_title' => $page_title,
                'page_description' => $page_desc,
                'post_loaders' => $r_post_loader,
            ];
        }

        $response = new ModifiedResourceResponse($r_pages);

        return $response;
    }


}

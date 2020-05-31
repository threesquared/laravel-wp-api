<?php namespace Threesquared\LaravelWpApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WpApi
{

    /**
     * Guzzle client
     * @var Client
     */
    protected $client;

    /**
     * WP-WPI endpoint URL
     * @var string
     */
    protected $endpoint;

    /**
     * Auth headers
     * @var string
     */
    protected $auth;

    /**
     * Constructor
     *
     * @param string $endpoint
     * @param Client $client
     * @param string $auth
     */
    public function __construct($endpoint, $auth = null)
    {
        $this->endpoint = $endpoint;
        $this->auth     = $auth;
        $this->client   = new Client();
    }

    /**
     * Get all posts
     *
     * @param  int $page
     * @return array
     */
    public function posts($page = null)
    {
        return $this->get('posts', ['page' => $page]);
    }

    /**
     * Get all pages
     *
     * @param  int $page
     * @return array
     */
    public function pages($page = null)
    {
        return $this->get('posts', ['type' => 'page', 'page' => $page]);
    }

    /**
     * Get post by id
     *
     * @param  int $id
     * @return array
     */
    public function postId($id)
    {
        return $this->get("posts/$id");
    }

    /**
     * Get post by slug
     *
     * @param  string $slug
     * @return array
     */
    public function post($slug)
    {
        return $this->get('posts', ['filter' => ['name' => $slug]]);
    }

    /**
     * Get page by slug
     *
     * @param  string $slug
     * @return array
     */
    public function page($slug)
    {
        return $this->get('posts', ['type' => 'page', 'filter' => ['name' => $slug]]);
    }

    /**
     * Get all categories
     *
     * @return array
     */
    public function categories()
    {
        return $this->get('categories');
    }

    /**
     * Get all tags
     *
     * @return array
     */
    public function tags()
    {
        return $this->get('taxonomies/post_tag/terms');
    }

    /**
     * Get posts from category
     *
     * @param  string $slug
     * @param  int $page
     * @return array
     */
    public function categoryPosts($slug, $page = null)
    {
        return $this->get('posts', ['page' => $page, 'filter' => ['category_name' => $slug]]);
    }

    /**
     * Get posts by author
     *
     * @param  string $name
     * @param  int $page
     * @return array
     */
    public function authorPosts($name, $page = null)
    {
        return $this->get('posts', ['page' => $page, 'filter' => ['author_name' => $name]]);
    }

    /**
     * Get posts tagged with tag
     *
     * @param  string $tags
     * @param  int $page
     * @return array
     */
    public function tagPosts($tags, $page = null)
    {
        return $this->get('posts', ['page' => $page, 'filter' => ['tag' => $tags]]);
    }

    /**
     * Search posts
     *
     * @param  string $query
     * @param  int $page
     * @return array
     */
    public function search($query, $page = null)
    {
        return $this->get('posts', ['page' => $page, 'filter' => ['s' => $query]]);
    }

    /**
     * Get posts by date
     *
     * @param  int $year
     * @param  int $month
     * @param  int $page
     * @return array
     */
    public function archive($year, $month, $page = null)
    {
        return $this->get('posts', ['page' => $page, 'filter' => ['year' => $year, 'monthnum' => $month]]);
    }

    /**
     * Get data from the API
     *
     * @param  string $method
     * @param  array  $query
     * @return array
     */
    public function get($method, array $query = array())
    {

        try {

            $query = ['query' => $query];

            if ($this->auth) {
                $query['auth'] = $this->auth;
            }

            $response = $this->client->get($this->endpoint . $method, $query);

            $return = [
                'results' => json_decode((string) $response->getBody(), true),
                'total'   => $response->getHeaderLine('X-WP-Total'),
                'pages'   => $response->getHeaderLine('X-WP-TotalPages')
            ];

        } catch (RequestException $e) {

            $error['message'] = $e->getMessage();

            if ($e->getResponse()) {
                $error['code'] = $e->getResponse()->getStatusCode();
            }

            $return = [
                'error'   => $error,
                'results' => [],
                'total'   => 0,
                'pages'   => 0
            ];

        }

        return $return;

    }
}

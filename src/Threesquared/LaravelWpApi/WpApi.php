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
    public function __construct($endpoint, Client $client, $auth = null)
    {
        $this->endpoint = $endpoint;
        $this->client   = $client;
        $this->auth     = $auth;
    }

    public function posts($page = null)
    {
        return $this->get('posts', ['page' => $page]);
    }

    public function pages($page = null)
    {
        return $this->get('posts', ['type' => 'page', 'page' => $page]);
    }

    public function post($slug)
    {
        return $this->get('posts', ['filter' => ['name' => $slug]]);
    }

    public function page($slug)
    {
        return $this->get('posts', ['type' => 'page', 'filter' => ['name' => $slug]]);
    }

    public function categories()
    {
        return $this->get('taxonomies/category/terms');
    }

    public function tags()
    {
        return $this->get('taxonomies/post_tag/terms');
    }

    public function categoryPosts($slug, $page = null)
    {
        return $this->get('posts', ['page' => $page, 'filter' => ['category_name' => $slug]]);
    }

    public function authorPosts($name, $page = null)
    {
        return $this->get('posts', ['page' => $page, 'filter' => ['author_name' => $name]]);
    }

    public function tagPosts($tags, $page = null)
    {
        return $this->get('posts', ['page' => $page, 'filter' => ['tag' => $tags]]);
    }

    public function search($query, $page = null)
    {
        return $this->get('posts', ['page' => $page, 'filter' => ['s' => $query]]);
    }

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

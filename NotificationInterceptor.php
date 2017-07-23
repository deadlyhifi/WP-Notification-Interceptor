<?php namespace JTV\Helpers;

/**
 *    This class will allow a page to be set and then a variable intercepted.
 *
 *    Use:
 *    class NewClassName extends \JTV\Helpers\QueryInterceptor
 *    {
 *        public $path = 'url-path';
 *
 *        public function intercept($query)
 *        {
 *            echo "MADE IT with " . $query;
 *        }
 *    }
 *
 *    Flush rewrite rules!
 *    http://www.php.net/manual/en/language.oop5.abstract.php
 */

abstract class NotificationInterceptor
{
    abstract public function intercept($argument);

    final public function __construct()
    {
        if (! isset($this->path)) {
            throw new \Exception(__CLASS__ . ' must have a $path variable defined.');
        }

        add_filter('rewrite_rules_array', [$this, 'addRewriteRules']);
        add_filter('query_vars', [$this, 'addQueryVars']);
        add_action('parse_request', [$this, 'calcPath']);
    }

    public function calcPath($query)
    {
        if (isset($query->query_vars['pagename'])
            && $query->query_vars['pagename'] === $this->path
        ) {
            add_filter('wp_headers', [$this, 'amendResponseHeaders']);
            add_action('template_redirect', [$this, 'templateRedirect']);
        }
    }

    // Add the new rewrite rule to existings ones
    final public function addRewriteRules($rules)
    {
        return [
            '(' . $this->path . ')/([^/]+)/?$' => 'index.php?pagename=' . $this->path . '&key=$matches[2]'
        ] + $rules;
    }

    // Add the URL
    final public function addQueryVars($vars)
    {
        array_push($vars, 'key');
        return $vars;
    }

    final public function amendResponseHeaders($headers)
    {
        if ($this->contentType === 'json') {
            $headers['Content-Type'] = 'application/json;charset=utf-8';
        }

        return $headers;
    }

    final public function templateRedirect()
    {
        global $wp_query;

        status_header(200);

        $this->intercept($wp_query->query['key']);
        exit();
    }
}

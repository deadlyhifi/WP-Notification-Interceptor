<?php

abstract class NotificationInterceptor {

	abstract public function intercept($argument);

	public final function __construct()
	{
		if(!isset($this->path))
			throw new Exception(__CLASS__ . ' must have a $path variable defined.');

		add_filter('query_vars', [$this, 'add_query_vars']);
		add_filter('rewrite_rules_array', [$this, 'add_rewrite_rules']);
		add_action('template_redirect', [$this, 'template_redirect']);
	}

	// Add the URL
	public final function add_query_vars($vars)
	{
	    $vars[] = $this->path;
		return $vars;
	}

	// Add the new rewrite rule to existing ones.
	public final function add_rewrite_rules($rules)
	{
	    $new_rules = [$this->path . '/([^/]+)/?$' => 'index.php?' . $this->path . '=$matches[1]'];
	    $rules = $new_rules + $rules;

	    return $rules;
	}

	// fire function that will act upon url query.
	public final function template_redirect()
	{
		global $wp_query;

		if(isset($wp_query->query_vars[$this->path])) {
			$this->intercept(get_query_var($this->path));
			exit();
		}
	}

}
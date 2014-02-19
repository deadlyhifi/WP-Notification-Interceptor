# WP-Custom-Query-Interceptor

Intercept a notification from external web-services, then act upon it.

Define a unique page to send notifications to,
the page will be registered with WordPress as part of the rewrite rules array but not show in the admin area.
Act upon that notification based on the URL paramater.

e.g. `http://example.com/very-random-string-to-make-sure-its-unique/paramater/`

##Usage

Include the `abstract QueryInterceptor` class somewhere. Then extend it:

    class WebServiceInterceptor extends QueryInterceptor
    {
        public $path = NOTIFICATION_PATH;

        public function intercept($query)
        {
            echo $this->path . "\n\n";
            echo "Argument : " . $query;
        }

    }

##Hyper Important!

When you first implement it, and after any changes to the `$path` variable **you must flush your permalink settings** or you'll be wondering why it’s not working, and you’ll tear out your hair (for which I accept no responsibility).

You could hook that into your plugin activation by flushing the rewrite rules with [flush_rewrite_rules](https://codex.wordpress.org/Function_Reference/flush_rewrite_rules_) on [register_activation_hook](https://codex.wordpress.org/Function_Reference/register_activation_hook) if you were so inclined.

##Notes

Use a very unique string as your path. You don't want anyone to accidentaly replicate it by making a page with that name through the admin area.

You might check to see if any data sent to the URL and redirect home if not with:

    $callback = file_get_contents('php://input');

    if ( ! $callback) {
        wp_redirect(home_url());
        exit;
    }

If you forget to set a `$path` an Exception will be thrown; the results of which could cause a chain reaction that would unravel the very fabric of the space-time continuum and destroy the entire universe!… Granted, that's the worst-case scenario. The destruction however might be limited merely to our own galaxy.

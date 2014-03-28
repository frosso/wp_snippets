wp_snippets
===========

My personal Snippets for Wordpress

## Percent Complete ##
A picture is worth a thousand words.
![Imgur](http://i.imgur.com/riovtA6.png)

## Controllers ##
Controllers for the admin area.

I usually use them to let the use interact with the plugins that I write. Extending the base controller, I find easier to create new option pages.

You can return a view and some variables to show when you're done with your controller method. The base controller will take care of rendering it.

## Repositories ##
Repositories to interact with Posts and metadata or directly with tables.

Usually I provide a method to filter every table column (or post metadata).

`MyAbstractPostRepository` uses `WP_Query` to interact with posts. I usually write a class for every Custom Post Type I have to interact with.

`MyAbstractTableRepository` uses `$wpdb` to interact with the database. I usually write a class for every table I need to interact with.

I find this easier so I can write more readable code. For example, I could write something like


    $movies = MovieRepository::query()->year(200)->result();

Or, if I have a custom table, I could write something like

    $rating = MovieTableRepository::query()->year(2000)->avg('rating');

## Resources ##
Classes that I use to create pages in the `posts` table when my plugin is activated or to create Custom Post Types.

For example, if I needed to create an About us page when my plugin is activated, I can use `MyAbstractPage` class. And I can use the same class to get that page ID (and interact with it in my theme), without worrying that if the post ID or the post title is changed I won't get the same result anymore. Also, if I have the WPML plugin activated, I provided an helper class that returns the page id for the current language. So I can write in my theme `AboutPage::getId()`.

Also, providing a template path, I can store the page's view in my plugin directory.

## Helpers ##

Just some helper functions. I find `user_is` and `get_called_class` (since we have a machine at work with an old PHP version) really useful.

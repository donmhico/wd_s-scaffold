wp scaffold wd_s
=======================

Generates a new theme based on wd_s.

## Installation

~~~bash
wp package install donmhico/wd_s-scaffold:"1.0.7"
~~~

## Usage

The minimum requirement of the command is a Theme Name, take note of enclosing it with double quotes `""`.

~~~bash
wp scaffold wd_s "<Theme Name>"
~~~

## OPTIONS

	<Theme Name>
		What to put in the 'Theme Name:' header in 'style.css'.

	[--slug]
		The slug for the new theme, used for prefixing functions.

	[--description=<description>]
		Theme description.

	[--theme_uri=<theme_uri>]
		Theme URI.

	[--author=<author>]
		Theme author.

	[--author_email=<author_email>]
		Author's email.

	[--author_uri=<author_uri>]
		Author URI.

	[--dev_uri=<dev_uri>]
		Developer URI.

	[--activate]
		Activate the newly generated theme.

### EXAMPLES

```bash
    # Generate a new wd_s starter theme with theme name "Acme Theme" and slug "acme".
    $ wp scaffold wd_s "Acme Theme" --slug="acme"
    Success: Created theme 'Acme Theme'.
```

```bash
    # Generate a new wd_s starter theme with theme name "Awesome Theme", slug "awesome", and a description.
    $ wp scaffold wd_s "Awesome Theme" --slug="awesome" --description="This is an awesome theme only for you."
    Success: Created theme 'Awesome Theme'.
```

## Special thanks

This package is heavily inspired by `wp scaffold underscores` command.

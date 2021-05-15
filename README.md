wp scaffold wd_s
=======================

Generates a new theme based on wd_s.

## Usage

~~~
wp scaffold <slug> [--activate] [--theme_name=<name>]
~~~

**OPTIONS**

	<slug>
		The slug for the new theme, used for prefixing functions.

	[--activate]
		Activate the newly generated theme.

	[--theme_name=<name>]
		What to put in the 'Theme Name:' header in 'style.css'.

**EXAMPLES**

	# Generate a new wd_s starter theme with theme name "Acme Theme" and slug "acme".
	$ wp scaffold wd_s acme --theme_name="Acme Theme"
	Success: Created theme 'Acme Theme'.

## Special thanks

This package is heavily inspired by `wp scaffold underscores` command.
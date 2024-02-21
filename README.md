# WebLoader
Component for CSS and JS files loading

-----

[![Licence](https://img.shields.io/packagist/l/surda/webloader-jm.svg?style=flat-square)](https://packagist.org/packages/surda/webloader-jm)
[![Latest stable](https://img.shields.io/packagist/v/surda/webloader-jm.svg?style=flat-square)](https://packagist.org/packages/surda/webloader-jm)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

## Installation

The recommended way to is via Composer:

```
composer require surda/webloader-jm
```

## Example with Nette Framework extension used

Configuration in `config.neon`:

```yaml
extensions:
	webloader: WebLoader\Nette\Extension

services:
	wlCssFilter: WebLoader\Filter\CssUrlsFilter(%wwwDir%)
	lessFilter: WebLoader\Filter\LessFilter
	jwlCssMinFilter: Joseki\Webloader\CssMinFilter

webloader:
	css:
		default:
			files:
				- style.css
				- {files: ["*.css", "*.less"], from: %appDir%/presenters} # Nette\Utils\Finder support
			filters:
				- @jwlCssMinFilter
			fileFilters:
				- @lessFilter
				- @wlCssFilter
			watchFiles:		# only watch modify file
				- {files: ["*.css", "*.less"], from: css}
				- {files: ["*.css", "*.less"], in: css}

	js:
		default:
			remoteFiles:
				- http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js
				- http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js
			files:
				- %appDir%/../libs/nette/nette/client-side/netteForms.js
				- web.js
```

Usage in presenter:

```php
class BasePresenter extends Presenter
{
	/** @var \WebLoader\Nette\LoaderFactory @inject */
	public $webLoader;

	/** @return CssLoader */
	protected function createComponentCss()
	{
		return $this->webLoader->createCssLoader('default');
	}

	/** @return JavaScriptLoader */
	protected function createComponentJs()
	{
		return $this->webLoader->createJavaScriptLoader('default');
	}
}
```

Template:

```html
{control css}
{control js}
```

## Example

Control factory in Nette presenter:

```php
protected function createComponentCss()
{
	$files = new WebLoader\FileCollection(WWW_DIR . '/css');
	$files->addFiles(array(
		'style.css',
		WWW_DIR . '/colorbox/colorbox.css',
	));

	$files->addWatchFiles(Finder::findFiles('*.css', '*.less')->in(WWW_DIR . '/css'));

	$compiler = WebLoader\Compiler::createCssCompiler($files, WWW_DIR . '/temp');

	$compiler->addFilter(new WebLoader\Filter\VariablesFilter(array('foo' => 'bar')));
	$compiler->addFilter(function ($code) {
		return cssmin::minify($code, "remove-last-semicolon");
	});

	$control = new WebLoader\Nette\CssLoader($compiler, '/webtemp');
	$control->setMedia('screen');

	return $control;
}
```

Template:

```html
{control css}
```
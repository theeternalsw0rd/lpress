This is a CMS package for Laravel 5, built from the ground up. Development is at initial stages, so don't expect anything to work yet.
At least Laravel 5.0.x required.

This package depends on "laravelcollective/html": "~5.0"

In order to get this package and its dependencies to load, please add

'Collective\Html\HtmlServiceProvider',
'EternalSword\LPress\LPressServiceProvider',

to your app config service providers list.

and add

'HTML'      => 'Collective\Html\HtmlFacade',
'Form'      => 'Collective\Html\FormFacade',

to your app config aliases array.

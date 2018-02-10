<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "home";
$route['404_override'] = '';
$route['playertrack'] = 'pages/playerCount';
$route['contact'] = 'home/contact';
$route['track'] = 'tracker/track';
$route['track/history/(:any)'] = 'tracker/history/$1';
$route['track/(:any)'] = 'tracker/track/$1';
$route['records'] = 'home/records';
$route['records/(:any)'] = 'home/records/$1';
$route['top'] = 'home/top50';
$route['top/history'] = 'home/top50history';
$route['top/history/(:num)'] = 'home/top50history/$1';
$route['top/(:num)'] = 'home/top50/$1';
$route['sig/(:any)'] = 'signature/sig/$1';
$route['sig/(:any)/(:any)'] = 'signature/sig/$1/$2';

$route['maintenance'] = 'home/maintenance';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
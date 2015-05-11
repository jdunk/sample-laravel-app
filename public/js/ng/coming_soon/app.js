APP = angular.module('APP', [
  "ngAnimate",
  "restangular",
  "ui.router",
  "acme.home"
])
  .config([
    '$stateProvider',
    '$urlRouterProvider',
    '$locationProvider',
    'RestangularProvider',
  function(
    $stateProvider,
    $urlRouterProvider,
    $locationProvider,
    RestangularProvider
  ) {

    var dir = '/js/ng/coming_soon/';

    $urlRouterProvider.otherwise("/");
    $locationProvider.html5Mode(true);

    // trigger json response from service
    RestangularProvider.setDefaultHeaders({"X-Requested-With": "XMLHttpRequest"});

    $stateProvider
      .state('home', {
        url: "/",
        templateUrl: dir + 'home.html',
        controller: 'HomeController'
      });
  }]);

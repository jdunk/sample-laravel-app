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

(function() {

  angular.module('acme.home', ['ngAnimate'])

    .controller('HomeController', [
    '$scope', '$location', 'Restangular',
    function($scope, $location, Restangular) {

      // TODO: modal form?

      $scope.submitted = false;

      $scope.signUp = function() {
        var raEmailSubscriber = Restangular.restangularizeElement(null, {
          email: $scope.email
        }, 'email_subscribers');

        raEmailSubscriber.save()
          .then(function(emailSubscriber) {
            // success
            $scope.submitted = true;
          }, function(error) {
            if (error.data && error.data.errors) {
              if (error.data.errors.email) {
                $scope.errors = error.data.errors.email;
              }

            }
          });
      }



    }]);

})();

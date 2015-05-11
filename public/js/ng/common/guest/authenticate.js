(function () {
  'use strict';

  angular.module('nd.guest.authenticate', [
    'nd.guest.login',
    'nd.guest.register',
    'ngSanitize',
    'ngAnimate',
    'ui.bootstrap.showErrors',
    'nd.alerts',
    'nd.focus'
  ])
  /**
   * Display UI for login/registration and handle response
   */
    .directive('ndAuthenticate', [
      'GuestLogin',
      'GuestRegister',
      'AlertService', function (GuestLogin, GuestRegister, AlertService) {
        return {
          restrict: 'AE',
          scope: {
            ndCurrentUser: '=?'
          },
          replace: true,
          link: function ($scope, $element, $attr) {
            $scope.rememberMe = true;
            $scope.usernameAvailable = true;

            $scope.changeMode = function (mode) {
              $scope.mode = mode;

              var modes = ['login', 'register', 'forgot'];

              for (var i = 0; i < modes.length; i++) {
                $scope[modes[i] + 'Focus'] = false;
              }

              $scope[mode + 'Focus'] = true;
            };

            // initial mode
            $scope.changeMode($attr.ndAuthenticateMode || 'login');

            $scope.login = function () {
              GuestLogin.authenticate({
                login: $scope.loginUsername,
                password: $scope.loginPassword,
                remember_me: $scope.rememberMe
              })
                .then(function (result) {
                  $scope.ndCurrentUser = result;
                }, function (reason) {
                  var error = 'Unknown error';

                  $scope.loginPassword = '';

                  if (reason.data && reason.data.message) {
                    error = reason.data.message;
                  }

                  AlertService.publish('warning', error, 'authentication');
                });
            };

            $scope.register = function () {
              GuestRegister.register({
                username: $scope.registerUsername,
                password: $scope.registerPassword,
                email: $scope.registerEmail
              })
                .then(function (result) {
                  console.log('Success: ', result);
                }, function (reason) {
                  console.log('Failed: ', reason);
                });
            };

            $scope.forgot = function() {
              GuestLogin.remind({
                email: $scope.resetEmail
              })
                .then(function (result) {
                  $scope.resetEmail = '';
                  AlertService.publish('success', result.data.message, 'authentication');
                }, function (reason) {
                  var error = 'Unknown error.';

                  $scope.resetEmail = '';

                  if (reason.data && reason.data.message) {
                    error = reason.data.message;
                  }

                  AlertService.publish('warning', error, 'authentication');
                });
            };

          },
          templateUrl: '/js/ng/common/guest/authenticate.html'
        };
      }]);

})();

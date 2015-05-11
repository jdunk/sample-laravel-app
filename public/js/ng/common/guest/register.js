(function() {
  'use strict';

  angular.module('nd.guest.register', [

  ])
    .factory('GuestRegister', ['$http', '$q', 'ErrorMessages', 'ErrorMessagesExcludedKeys',
      function($http, $q, ErrorMessages, ErrorMessagesExcludedKeys) {

      ErrorMessages.usernameTaken = 'This username is already in use';
      ErrorMessagesExcludedKeys.push('usernameTaken');

      var usernameAvailabilityRequestPromises = [];

      return {
        register: function(params) {
          return $http.post('/registrations', params, {
            headers: {}
          });
        },
        usernameAvailable: function(username) {
          for (var i = 0; i < usernameAvailabilityRequestPromises.length; i++) {
            var requestDeferred = usernameAvailabilityRequestPromises[i];

            requestDeferred.resolve({
              data: {
              available: true
              }
            });

            usernameAvailabilityRequestPromises.splice(usernameAvailabilityRequestPromises.indexOf(requestDeferred), 1);
          }

          var deferred = $q.defer();
          usernameAvailabilityRequestPromises.push(deferred);

          return $http.get('registrations/' + username, {
            headers: {},
            timeout: deferred.promise
          });
        }
      };
    }])
    .directive('usernameTaken', ['GuestRegister', 'ErrorMessages', function (GuestRegister) {
      return {
        require: 'ngModel',
        link: function (scope, elem, attrs, ctrl) {

          elem.on('keyup', function (evt) {
            scope.$apply(function () {
              var val = elem.val();
              if (!val) {
                ctrl.$setValidity('usernameTaken', true);
                return;
              }

              GuestRegister.usernameAvailable(val)
                .success(function(data, status, headers, config) {
                  ctrl.$setValidity('usernameTaken', data.available);
                });
            });
          });
        }
      };
    }]);

})();

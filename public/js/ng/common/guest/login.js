(function() {
  'use strict';

  angular.module('nd.guest.login', [

  ])
    .factory('GuestLogin', ['$http', function($http) {
      return {
        authenticate: function(params) {
          return $http.post('/sessions', params, {
            headers: {}
          });
        },
        remind: function(params) {
          return $http.post('/password/remind', params, {
            headers: {}
          });
        }
      };
    }]);

})();

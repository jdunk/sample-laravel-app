(function () {
  'use strict';

  angular.module('nd.alerts', [

  ])
  /**
   * Show alerts anywhere.  Alert pubsub.  You got zones and stuff:
   *
   * <nd-alert nd-alert-zone="registration" />
   *
   * Then in your codes:
   *
   * AlertService.publish('info', 'This is something.', 'registration');
   *
   */
    .factory('AlertService', function() {
      var observers = {};

      return {
        observe: function(zone, cb) {
          if (!observers[zone]) {
            observers[zone] = [];
          }

          if (typeof cb === 'function') {
            observers[zone].push(cb);
          } else {
            console.warn('Cannot observe with that callback because it is not a function.', cb);
          }
        },
        clear: function() {
          this.onClear();
        },
        onClear: function() {
          this.publish(null, null);
        },
        publish: function(type, message, zone) {
          if (!zone) {
            zone = 'global';
          }

          if (!observers[zone]) {
            return;
          }

          for (var i = 0; i < observers[zone].length; i++) {
            var obj = observers[zone][i];

            if (typeof obj === 'function') {
              obj(type, message);
            } else {
              console.warn('observer callback is not function');
            }
          }
        }
      }
    })
    .directive('ndAlert', [
      'AlertService',
      function (AlertService) {
        return {
          restrict: 'AE',
          scope: {

          },
          replace: true,
          link: function ($scope, $element, $attr) {

              var zone = $attr.ndAlertZone || 'global';

              AlertService.observe(zone, function(type, message) {
                $scope.message = message;
                $scope.type = type;
              });

              AlertService.onClear(function() {
                $scope.message = null;
              });

              $scope.dismiss = function() {
                $scope.message = null;
              }
          },
          //template: '<div ng-include="getTemplateUrl()"></div>'
          template: '<div class="nd-alert"><div ng-show="message" class="alert alert-{{type}}" role="alert"><button type="button" class="close" nd-click="dismiss()" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{message}}</div></div>'
        };
      }]);

})();

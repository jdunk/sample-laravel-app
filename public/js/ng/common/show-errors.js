(function() {
  var showErrorsModule;

  showErrorsModule = angular.module('ui.bootstrap.showErrors', []);

  showErrorsModule.directive('showErrors', [
    '$timeout', 'showErrorsConfig', '$interpolate', function($timeout, showErrorsConfig, $interpolate) {
      var getShowSuccess, getTrigger, linkFn;
      getTrigger = function(options) {
        var trigger;
        trigger = showErrorsConfig.trigger;
        if (options && (options.trigger != null)) {
          trigger = options.trigger;
        }
        return trigger;
      };
      getShowSuccess = function(options) {
        var showSuccess;
        showSuccess = showErrorsConfig.showSuccess;
        if (options && (options.showSuccess != null)) {
          showSuccess = options.showSuccess;
        }
        return showSuccess;
      };
      linkFn = function(scope, el, attrs, formCtrl) {
        var blurred, inputEl, inputName, inputNgEl, options, showSuccess, toggleClasses, trigger;
        blurred = false;
        options = scope.$eval(attrs.showErrors);
        showSuccess = getShowSuccess(options);
        trigger = getTrigger(options);
        inputEl = el[0].querySelector('.form-control[name]');
        inputNgEl = angular.element(inputEl);
        inputName = $interpolate(inputNgEl.attr('name') || '')(scope);
        if (!inputName) {
          throw "show-errors element has no child input elements with a 'name' attribute and a 'form-control' class";
        }
        inputNgEl.bind(trigger, function() {
          blurred = true;
          return toggleClasses(formCtrl[inputName].$invalid);
        });
        scope.$watch(function() {
          return formCtrl[inputName] && formCtrl[inputName].$invalid;
        }, function(invalid) {
          if (!blurred) {
            return;
          }
          return toggleClasses(invalid);
        });
        scope.$on('show-errors-check-validity', function() {
          return toggleClasses(formCtrl[inputName].$invalid);
        });
        scope.$on('show-errors-reset', function() {
          return $timeout(function() {
            el.removeClass('has-error');
            el.removeClass('has-success');
            return blurred = false;
          }, 0, false);
        });
        return toggleClasses = function(invalid) {
          el.toggleClass('has-error', invalid);
          if (showSuccess) {
            return el.toggleClass('has-success', !invalid);
          }
        };
      };
      return {
        restrict: 'A',
        require: '^form',
        compile: function(elem, attrs) {
          if (!elem.hasClass('form-group')) {
            throw "show-errors element does not have the 'form-group' class";
          }
          return linkFn;
        }
      };
    }
  ]);

  /**
   * Validation Error Messages
   *
   * Inject 'ErrorMessages'
   */
  showErrorsModule.value('ErrorMessages', {
    'email': 'Invalid email',
    'max': 'Maximum value: ',
    'maxlength': 'Maximum length: ',
    'min': 'Minimum value: ',
    'minlength': 'Minimum length: ',
    'required': 'This field cannot be blank',
    'unique': 'This field does not allow duplicated values',
    'pattern': 'Invalid format'
  })

  /**
   * Keys for which we should NOT show the parameter values i.e. "Max length: 7"
   *
   * Inject 'ErrorMessagesExcludedKeys'
   */
  showErrorsModule.value('ErrorMessagesExcludedKeys', [
    'pattern'
  ]);

  showErrorsModule.directive('uiValidationErrorMessages', ['ErrorMessages', 'ErrorMessagesExcludedKeys', '$parse',
    function (ErrorMessages, ErrorMessagesExcludedKeys, $parse) {
      var normalizeAttrName = function(attr) {
        return attr.replace(/^(x[\:\-_]|data[\:\-_]|ng[\:\-_])/i, '');
      };

      return {
        restrict: 'E',
        require: '^form',
        replace: true,
        template:
        '<div class="help-block" ng-show="fieldCtrl.$invalid">' +
        ' <small ng-repeat="(errorType, errorValue) in fieldCtrl.$error" ng-show="fieldCtrl.$error[errorType]">{{errorMessages[errorType]}} <span ng-show="ErrorMessagesExcludedKeys.indexOf(errorType) < 0" ng-bind="{{errorType}}"></span></small>' +
        '</div>',
        scope: {},
        link: function(scope, element, attrs, formCtrl) {
          var formGroup = element.parent();
          var inputElement = formGroup[0].querySelector('input[name],select[name],textarea[name]');

          if(!inputElement) {
            throw 'ui-validation-error-messages requires a sibling input/select/textarea element with a \'name\' attribute';
          }

          var angularElement = angular.element(inputElement);

          scope.ErrorMessagesExcludedKeys = ErrorMessagesExcludedKeys;

          scope.fieldCtrl = formCtrl[inputElement.name];
          angular.forEach(angularElement[0].attributes, function(attr) {
            //ngRequired value cannot appear in error message
            if(/^(ng-required)/.test(attr.name)) {
              return;
            }

            var attrName = normalizeAttrName(attr.name);
            var attrValue = attr.nodeValue;

            if(ErrorMessages.hasOwnProperty(attrName) && attrValue.length > 0) {
              scope[attrName] = attrValue;

              var canBeAModel = /^[A-Za-z]/.test(attr.nodeValue);

              if(canBeAModel) {
                scope[attrName] = $parse(attrValue)(scope.$parent);

                scope.$parent.$watch(attr.nodeValue, function(newValue) {
                  scope[attr.name] = newValue;
                });
              }
            }
          });

          scope.errorMessages = angular.copy(ErrorMessages);
        }
      };
    }
  ]);

  showErrorsModule.provider('showErrorsConfig', function() {
    var _showSuccess, _trigger;
    _showSuccess = false;
    _trigger = 'blur';
    this.showSuccess = function(showSuccess) {
      return _showSuccess = showSuccess;
    };
    this.trigger = function(trigger) {
      return _trigger = trigger;
    };
    this.$get = function() {
      return {
        showSuccess: _showSuccess,
        trigger: _trigger
      };
    };
  });

}).call(this);

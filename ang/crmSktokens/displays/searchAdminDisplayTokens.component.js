(function(angular, $, _) {
  "use strict";

  angular.module('crmSearchAdmin').component('searchAdminDisplayTokens', {
    bindings: {
      display: '<',
      apiEntity: '<',
      apiParams: '<'
    },
    require: {
      parent: '^crmSearchAdminDisplay'
    },
    templateUrl: '~/crmSktokens/displays/searchAdminDisplayTokens.html',
    controller: function($scope, searchMeta, crmUiHelp) {
      var ts = $scope.ts = CRM.ts('org.civicrm.search_kit'),
        ctrl = this;
      $scope.hs = crmUiHelp({file: 'CRM/Search/Help/Display'});

      this.$onInit = function () {
        if (!ctrl.display.settings) {
          ctrl.display.settings = {
          };
        }
        ctrl.parent.initColumns({});
      };

      this.$onRewrite = function(col) {
        if (col.rewrite) {
          ctrl.parent.toggleRewrite(col);
        }
      };

    }
  });

})(angular, CRM.$, CRM._);

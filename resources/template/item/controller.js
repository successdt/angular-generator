'use strict';

/* Controllers */
app.controller('$indexControllerNameCreateCtrl', function($scope, $http, $state, APP_CONFIG, SessionService,
                toaster, $location, $stateParams, CommonService, $indexControllerNameService, $rootScope, UserService) {
  $scope.data = {
    isLoading : false,
    item : {},
    isDisabled : !angular.isUndefined($stateParams.id),
    isEditing : !angular.isUndefined($stateParams.id),
    permission : UserService.generatePermissions('$permissionLink')
  }


  $scope.init = function () {
    $scope.data.isLoading = true;
    if($scope.data.isEditing) {
      var $id = $stateParams.id;

      $indexControllerNameService.view($id).then(function(response) {
        $scope.data.isLoading = false;
        if(response.success) {
          $scope.data.item = response.data;
        } else {
          toaster.pop('error', 'Lỗi', response.message);
        }
      });
    } else {
      // default value
    }
  }

  $scope.init();


  $scope.reset = function() {

  }

  $scope.save = function () {
    var $id = $scope.data.isEditing ? $stateParams.id : '';
    $indexControllerNameService.save($id, $scope.data.item).then(function(response) {
      $scope.data.isLoading = false;
      if(response.success) {
        toaster.pop('success', 'Thông tin', 'Lưu thành công');
        $rootScope.doTheBack();
      } else {
        toaster.pop('error', 'Lỗi', response.message);
      }
    });
  }
});
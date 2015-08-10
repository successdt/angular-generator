'use strict';

/* Controllers */
app.controller('$indexControllerNameCtrl', function($scope, $http, $state, APP_CONFIG, SessionService, toaster,
                                                    $indexControllerNameService, UserService) {
  $scope.data = {
    isLoading : false,
    permission : UserService.generatePermissions('$permissionLink'),
    items : []
  }


  $scope.init = function() {
    $scope.loadData();
  }

  // load data
  $scope.loadData = function() {
    $scope.data.isLoading = true;

    $indexControllerNameService.getList().then(function(response){
      $scope.data.isLoading = false;
      response = response.data;
      if(response.success) {
        $scope.data.items = response.data;
      } else {
        toaster.pop('error', 'Lỗi', response.message);
      }
    });
  }

  $scope.init();


  $scope.remove = function($index) {
    var $id = $scope.data.items[$index].id;

    if ( confirm('Bạn có chắc chắn muốn xóa không?') ) {
      $indexControllerNameService.remove($id).then(function( response ) {
        if(response.success) {
          $scope.data.items.splice($index, 1);
          toaster.pop('success', 'Thành công', 'Đã xóa ');
        } else {
          toaster.pop('error', 'Lỗi', response.message);
        }
      });
    }
  };
});
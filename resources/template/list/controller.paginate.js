'use strict';

/* Controllers */
app.controller('$indexControllerNameCtrl', function($scope, $http, $state, APP_CONFIG, SessionService, toaster,
                                           $location, $indexControllerNameService, CommonService, UserService) {
  $scope.data = {
    items: [],
    isLoading: false,
    export_url: '',
    filter: {},
    pagination: {},
    permission: UserService.generatePermissions('$permissionLink')
  }

  // list thi_sinh
  $scope.init = function() {
    $scope.data.isLoading = true;
    $indexControllerNameService.getSearchFilters().then(function(response) {
      $scope.data.filterParams = response;
    });
  }

  $scope.init();

  $scope.loadData = function(isReset) {
    $scope.data.isLoading = true;
    if (isReset) {
      $scope.data.pagination.currentPage = 1;
    }

    $indexControllerNameService.getList($scope.data).then(function(response){
      $scope.data.items = [];
      response = response.data;
      $scope.data.isLoading = false;
      if(response.success) {
        $scope.data.items = response.data;
        $scope.data.pagination = CommonService.transformPagination(response, angular.copy($scope.data.pagination));
      } else {
        toaster.pop('error', 'Lỗi', response.message);
      }
    });

  }

  $scope.remove = function (index) {
    var id = $scope.data.items[index].id;

    if(confirm('Bạn có chắc chắn không?')){
      $indexControllerNameService.remove(id).then(function(response) {
        if(response.success) {
          toaster.pop('success', 'Thông tin', 'Đã xóa thành công');
          $scope.data.items.splice(index, 1);
        } else {
          toaster.pop('error', 'Lỗi', response.message);
        }
      });
    }
  }

  $scope.reset = function () {
    $scope.data.dot_tuyen_sinh = $scope.data.ds_dot_tuyen_sinh[0];
    $scope.data.nganh_thac_si = null;
    $scope.data.co_so_dao_tao = null;

    $scope.loadData();
  }

  $scope.transformDate = function($date) {
    return CommonService.transformDate($date);
  }
})
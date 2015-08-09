'use strict';

angular.module('app')
  .service('$controllerNameService', ["$http", "SessionService", "APP_CONFIG", "$q", "CommonService", function($http, SessionService, APP_CONFIG, $q, CommonService) {

    this.getList = function($scopeData) {
      $scopeData = this.addFilterParams($scopeData);
      //$scopeData = CommonService.addPaginationParams($scopeData);
      return $http.get(APP_CONFIG.baseUrl + APP_CONFIG.$constant, {params : $scopeData.filter}).then(function(response){
        response = response.data;

        return response;
      });
    }

    this.remove = function($id) {
      return $http.delete(APP_CONFIG.baseUrl + APP_CONFIG.$constant + $id).then(function(response){
        response = response.data;

        return response;
      });
    }


    this.view = function($id) {
      return $http.get(APP_CONFIG.baseUrl + APP_CONFIG.$constant + $id).then(function(response){
        response = response.data;

        return response;
      });
    }


    this.save = function($id, $params) {
      return $http.post(APP_CONFIG.baseUrl + APP_CONFIG.$constant + $id, $.param($params)).then(function(response){
        response = response.data;

        return response;
      });
    }
	}]);

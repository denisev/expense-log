// Resource to communicate with the RESTful backend
// Use ng defaults
angular.module('expensesService', ['ngResource']).factory('ExpenseRecord', ['$resource',
                                                                            function($resource) {
  return $resource('service.php');
}]);

// Main module to handle two views
angular.module('expenses', ['expensesService','angularFileUpload','ngRoute'])
.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {
  $routeProvider
  // Default view - list all expense records
  .when('/', {
    templateUrl: 'expense-list.html',
    controller: 'ExpenseList',
  })
  // Detail view - CRUD
  .when('/edit/:recordId', {
    templateUrl: 'expense-edit.html',
    controller: 'ExpenseEdit',
  });
}])
// Global controller, set up the root scope
.controller('Expense', ['$rootScope','$location',function($rootScope,$location){
  $rootScope.go = function(location) {
    $location.path(location);
  };
}])
// List controller, very simple
.controller('ExpenseList', [ '$scope','ExpenseRecord', function($scope,ExpenseRecord) {
  $scope.expenseList = ExpenseRecord.query();
}])
// Detail view controller with handling of the file upload
.controller('ExpenseEdit', ['$scope','$routeParams','ExpenseRecord','$upload', function($scope,$routeParams,ExpenseRecord,$upload) {
  $scope.isNew = $routeParams.recordId == 'new'; // The view uses this to hide Delete button on when creating a new record
  $scope.expense = ExpenseRecord.get({recordId: $routeParams.recordId},function(resource){
    // Convert the ISO date string coming from the service to JS date
    resource.date = new Date(resource.tr_date);
  });
  $scope.save = function() {
	// Asynchronously post form to the service, resource contains service response 
    ExpenseRecord.save({},$scope.expense,function(resource){
      // If we have a file to upload, do it after we successfully create/update the record
      // Important in the case of creating a new record, because at the moment of posting the form, we don't have record Id yet
      // Here we have the resource id returned from the service
      if ( $scope.file ) {
	      $scope.upload = $upload.upload({
	    	  url: 'file.php',
	    	  data: {fileId:resource.id},
	    	  file: $scope.file
	      }).success(function(){
	    	  // only redirect to list on success
	          $scope.go('/');
	      });
      }
      else {
        // if there is no file to upload, redirect to list
        $scope.go('/');
      }
    });
  }
  $scope.remove = function() {
    ExpenseRecord.remove({recordId:$scope.expense.id});
    $scope.go('/');
  };
  $scope.onFileSelect = function($files) {
    // on file select, save the file for future form submit processing
    $scope.file = $files[0];
  }
}]);

<html ng-app="expenses">
<head>
<link rel="stylesheet" type="text/css"
  href="bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css"
  href="bootstrap/css/bootstrap-theme.css">
<style type="text/css">
.nav,.pagination,.carousel,.panel-title a {
	cursor: pointer;
}

.table-hover>tbody>tr:hover>td {
	cursor: pointer;
}
</style>
<script src="js/lib/underscore.js"></script>
<script src="js/lib/angular-file-upload-html5-shim.js"></script>
<script src="js/lib/angular.js"></script>
<script src="js/lib/angular-route.js"></script>
<script src="js/lib/angular-resource.js"></script>
<script src="js/lib/angular-file-upload.js"></script>
<script src="js/expenses.js"></script>
<title>Expense Log</title>
</head>
<body ng-controller="Expense">
  <div class="container">
    <div class="page-header">
      <h1>Expense Log</h1>
    </div>
    <div ng-view>
    </div>
  </div>
</body>
</html>
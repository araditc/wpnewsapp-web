<?php
include 'config.php';
$newsId = $_REQUEST["i"];
$newsId = htmlspecialchars($newsId);

include 'Funcsapi.php';
$mdl = new Funcsapi();

$data = $mdl->getNews($newsId,$params);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php echo $data["post_title"];?></title>
<noscript>
    <style type="text/css">
        .login-wrapper {display:none;}
    </style>
    <div class="noscriptmsg">
    	You don't have javascript enabled.  Good luck with that.
    	<br/>
    	 جاوااسکریپت مرورگر شما غیر فعال است .لطفا برای مشاهده سایت، 
    	در قسمت تنظیمات مرورگر خود جاوااسکریپت را فعال نموده و مجددا سایت را بارگذاری کنید
    </div>
</noscript>

<!--base css styles-->
<link rel="stylesheet" href="<?php echo MYURL;?>apinews/myfile/bootstrap.min.css">
<!--flaty css styles-->
<link rel="stylesheet" href="<?php echo MYURL;?>apinews/myfile/flaty.css">
<link rel="stylesheet" href="<?php echo MYURL;?>apinews/myfile/flaty-responsive.css">

</head>
<body class="login-page">
	<!-- BEGIN Main Content -->
	<div class="login-wrapper">
		
		<form id="form-login"  method="post">
			<div class="row">
				<div class="text-center col-xs-6">
				<?php echo $data["post_title"];?>
				</div>
				<div class="text-center col-xs-6">
					<?php echo $data["post_date"];?>
				</div>
			</div>
			<div class="row">
				<div class="text-center col-xs-1">&nbsp;</div>
				<div class="text-center col-xs-10" style="overflow:hidden">
					<img src="<?php echo $data["post_image"];?>" width="100%"/>
				</div>
				<div class="text-center col-xs-1">&nbsp;</div>
			</div>
			<div class="row">
				<div class="text-center col-xs-12">
					<?php echo $data["post_content"];?>
				</div>
			</div>
		</form>
	</div>
	<!-- END Main Content -->

	<!--basic scripts-->
	<script src="<?php echo MYURL;?>apinews/myfile/jquery.min.js"></script>
	<script>
		window.jQuery
				|| document
						.write('<script src="<?php echo MYURL;?>apinews/myfile/jquery-2.1.1.min.js"><\/script>')
	</script>
	<script src="<?php echo MYURL;?>apinews/myfile/bootstrap.min.js"></script>
</body>
</html>

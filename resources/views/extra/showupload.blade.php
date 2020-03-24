<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>腩潮鲜工牌生成</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
	</head>
	<body>
		<form action="/uploadimage" method="post"
		enctype="multipart/form-data">
		<label for="file">选择你的头像:</label>
		<input type="file" name="file" id="file" /> 
		<br />
		<input type="submit" name="submit" value="提交生成" />
		</form>
	</body>
</html>
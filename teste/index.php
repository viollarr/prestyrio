<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script src="autocomplete/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="autocomplete/jquery.autocomplete.js" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auto complete</title>
<script type="text/javascript">
  $(document).ready(function(){
    $('#auto').autocomplete("teste.php",
    {
      source: "buscaDados.php",
      minLength: 2
    });
  });
</script>

</head>

<body>

<form action="" enctype="multipart/form-data">
<input type="text" id="auto" />

</form>
</body>
</html>
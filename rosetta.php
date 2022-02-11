<html>
<head>
<title>Connecting Django and Java</title>
</head>
<body>
<h1>Connecting Django and Java</h1>
<ul>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/api/src/main/java/org/sakaiproject/plus/api/model/Tenant.java" target="_blank">Tenant Data Model</a> ↔️
<a href="https://github.com/csev/dj4e-samples/blob/main/autos/models.py" target="_blank">models.py</a>
</p></li>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/api/src/main/java/org/sakaiproject/plus/api/model/Context.java" target="_blank">Context Data Model</a>
</p></li>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/tool/src/main/java/org/sakaiproject/plus/tool/PlusConfiguration.java" target="_blank">PlusConfiguration</a> ↔️
(like WGSIConfig in PythonAnywhere)
</p></li>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/tool/src/main/java/org/sakaiproject/plus/tool/WebMvcConfiguration.java" target="_blank">WebMvcConfiguration.java</a> ↔️
<a href="https://github.com/csev/dj4e-samples/blob/main/dj4e-samples/settings.py" target="_blank">settings.py</a>
</p></li>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/tool/src/main/java/org/sakaiproject/plus/tool/MainController.java" target="_blank">MainController.java</a> ↔️
<a href="https://github.com/csev/dj4e-samples/blob/main/autos/views.py" target="_blank">views.py</a>
</p></li>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/tool/src/main/webapp/WEB-INF/templates/index.html" target="_blank">index.html</a>  ↔️ 
<a href="https://github.com/csev/dj4e-samples/blob/main/autos/templates/autos/auto_list.html" target="_blank">auto_list.html</a>
</p></li>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/tool/src/main/resources/Messages.properties" target="_blank">Messages.properties</a> (I18N)
</p></li>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/tool/src/main/webapp/WEB-INF/templates/tenant.html" target="_blank">tenant.html</a>  ↔️ 
(no detail page in autos)
</p></li>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/tool/src/main/webapp/WEB-INF/templates/form.html" target="_blank">form.html</a>  ↔️ 
<a href="https://github.com/csev/dj4e-samples/blob/main/autos/templates/autos/auto_form.html" target="_blank">auto_form.html</a>
</p></li>

<li><p>
<a href="https://github.com/csev/sakai/blob/plus/plus/tool/src/main/webapp/WEB-INF/templates/delete.html" target="_blank">delete.html</a>  ↔️ 
<a href="https://github.com/csev/dj4e-samples/blob/main/autos/templates/autos/auto_confirm_delete.html" target="_blank">auto_confirm_delete.html</a>
</p></li>

</ul>

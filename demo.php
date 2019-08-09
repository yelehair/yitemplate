<?php
use Yelehair\Yitemplate\Template;

require __DIR__.'/vendor/autoload.php';
/**
--普通变量
{$name}

--注释
{#}其中为注释内容...{#}

--if判断
{if ($a)}
{/if}

--if else 判断
{if ($a)}

{else}

{/if}

--foreach循环
{foreach $array(key,value)}
{@key}{@value}
{/foreach}

--include包含
{#include file="abc.php"}
 */

//创建网站根目录常量
define('ROOT_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
//模板文件目录
define('TPL_DIR', ROOT_PATH . 'templates' . DIRECTORY_SEPARATOR);
//编译文件目录
define('TPL_C_DIR', ROOT_PATH . 'templates_c' . DIRECTORY_SEPARATOR);
//缓存目录
define('CACHE', ROOT_PATH . 'cache' . DIRECTORY_SEPARATOR);
//是否开启缓存
define('IS_CACHE', false);
IS_CACHE ? ob_start() : NULL;
//实例化模版类
$template = new Template();
//注入变量
$template->assign('title', '标题');
$template->assign('content', '这是我输出的内容');
$template->assign('array', ['a'=>'这是1个a', 'b'=>'这是1个b']);
// index.tpl 文件内容如下，存放在上面定义的 TPL_DIR 目录里面
/**
 *
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>{$title}</title>
</head>
<body>
{$content}
<br>
{foreach $array(key,value)}
{@key}========={@value}<br>
{/foreach}
</body>
</html>
 */
//调用display方法
$template->display('index.tpl');
